<?php
session_start();
require_once 'includes/app_settings.php';
require_once 'classes/User.php';
require_once 'classes/Auth.php';
require_once 'config/database.php';

$appName = AppSettings::get('app_name', 'JobsMtaani');
$errors = [];
$success = '';

// Check if user is already logged in
if (Auth::isLoggedIn()) {
    $user = Auth::getCurrentUser();
    switch($user['user_type']) {
        case 'superadmin':
        case 'admin':
            header('Location: admin/');
            break;
        case 'service_provider':
            header('Location: provider/');
            break;
        case 'customer':
            header('Location: customer/');
            break;
        default:
            header('Location: dashboard.php');
    }
    exit;
}

// Check if we have a pending user from social login
if (!isset($_SESSION['pending_user_id'])) {
    header('Location: login.php');
    exit;
}

$pendingUserId = $_SESSION['pending_user_id'];
$pendingUserType = $_SESSION['pending_user_type'];

// Get user data
$database = new Database();
$db = $database->getConnection();
$userObj = new User($db);

$userData = $userObj->getUserById($pendingUserId);
if (!$userData) {
    header('Location: login.php');
    exit;
}

if ($_POST) {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $idNumber = trim($_POST['id_number'] ?? '');
    $idDocument = $_FILES['id_document'] ?? null;
    
    // Validation
    if (empty($firstName)) $errors[] = "First name is required";
    if (empty($lastName)) $errors[] = "Last name is required";
    if (empty($phone)) $errors[] = "Phone number is required";
    if (empty($address)) $errors[] = "Address is required";
    if (empty($city)) $errors[] = "City is required";
    if (empty($idNumber)) $errors[] = "ID number is required";
    
    // Handle ID document upload
    $idDocumentPath = null;
    if ($idDocument && $idDocument['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/id_documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileExtension = strtolower(pathinfo($idDocument['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors[] = "Invalid file type. Only JPG, JPEG, PNG, and PDF files are allowed.";
        } elseif ($idDocument['size'] > 5000000) { // 5MB limit
            $errors[] = "File size exceeds 5MB limit.";
        } else {
            $fileName = 'id_' . $pendingUserId . '_' . time() . '.' . $fileExtension;
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($idDocument['tmp_name'], $targetPath)) {
                $idDocumentPath = $targetPath;
            } else {
                $errors[] = "Failed to upload ID document.";
            }
        }
    } elseif ($idDocument && $idDocument['error'] !== UPLOAD_ERR_NO_FILE) {
        $errors[] = "Error uploading ID document.";
    }
    
    if (empty($errors)) {
        try {
            // Update user information
            $query = "UPDATE users 
                      SET first_name = :first_name, last_name = :last_name, phone = :phone
                      WHERE id = :user_id";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':first_name', $firstName);
            $stmt->bindParam(':last_name', $lastName);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':user_id', $pendingUserId);
            $stmt->execute();
            
            // Update user profile
            $query = "UPDATE user_profiles 
                      SET address = :address, city = :city, 
                          id_number = :id_number, id_document_path = :id_document_path
                      WHERE user_id = :user_id";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':city', $city);
            $stmt->bindParam(':id_number', $idNumber);
            $stmt->bindParam(':id_document_path', $idDocumentPath);
            $stmt->bindParam(':user_id', $pendingUserId);
            $stmt->execute();
            
            // If this is a provider, create provider profile if it doesn't exist
            if ($pendingUserType === 'service_provider') {
                $query = "SELECT COUNT(*) FROM provider_profiles WHERE user_id = :user_id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':user_id', $pendingUserId);
                $stmt->execute();
                
                if ($stmt->fetchColumn() == 0) {
                    $query = "INSERT INTO provider_profiles (user_id, status) VALUES (:user_id, 'pending')";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':user_id', $pendingUserId);
                    $stmt->execute();
                }
            }
            
            // Get updated user data
            $updatedUserData = $userObj->getUserById($pendingUserId);
            
            // Login the user
            Auth::login($updatedUserData);
            
            // Clear pending user session data
            unset($_SESSION['pending_user_id']);
            unset($_SESSION['pending_user_type']);
            
            // Redirect based on user type
            switch($updatedUserData['user_type']) {
                case 'superadmin':
                case 'admin':
                    header('Location: admin/');
                    break;
                case 'service_provider':
                    header('Location: provider/');
                    break;
                case 'customer':
                    header('Location: customer/');
                    break;
                default:
                    header('Location: dashboard.php');
            }
            exit;
            
        } catch (Exception $e) {
            $errors[] = "Failed to update profile: " . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<style>
body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.card {
    border-radius: 15px;
    border: none;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 50px;
    padding: 12px 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.form-control, .form-select {
    border-radius: 10px;
    padding: 12px 15px;
    border: 2px solid #e9ecef;
    transition: border-color 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
}

.input-group-text {
    background-color: #f8f9fa;
    border: 2px solid #e9ecef;
    border-right: none;
    border-radius: 10px 0 0 10px;
}

.input-group > .form-control:not(:first-child) {
    border-left: none;
    border-radius: 0 10px 10px 0;
}

.alert {
    border-radius: 10px;
}

.text-primary {
    color: #667eea !important;
}

.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.form-label {
    font-weight: 500;
    color: #495057;
}
</style>

<main class="py-5 min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-user-check fa-2x text-primary"></i>
                            </div>
                            <h2 class="display-6 fw-bold text-primary">Complete Your Profile</h2>
                            <p class="text-muted">Welcome! Please provide additional information to complete your registration.</p>
                        </div>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="POST" id="onboardingForm" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control form-control-lg" id="first_name" name="first_name" 
                                           value="<?php echo htmlspecialchars($userData['first_name'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control form-control-lg" id="last_name" name="last_name" 
                                           value="<?php echo htmlspecialchars($userData['last_name'] ?? ''); ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">+254</span>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>" 
                                           placeholder="712345678" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control form-control-lg" id="address" name="address" rows="3" 
                                          placeholder="Enter your full address" required><?php echo htmlspecialchars($userData['address'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control form-control-lg" id="city" name="city" 
                                           value="<?php echo htmlspecialchars($userData['city'] ?? ''); ?>" 
                                           placeholder="Enter your city" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="id_number" class="form-label">National ID Number</label>
                                    <input type="text" class="form-control form-control-lg" id="id_number" name="id_number" 
                                           value="<?php echo htmlspecialchars($_POST['id_number'] ?? ''); ?>" 
                                           placeholder="Enter your ID number" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="id_document" class="form-label">Upload National ID Document</label>
                                <input type="file" class="form-control form-control-lg" id="id_document" name="id_document" 
                                       accept=".jpg,.jpeg,.png,.pdf" required>
                                <div class="form-text">Upload a clear scan or photo of your National ID (JPG, PNG, or PDF, max 5MB)</div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>Complete Registration
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4">
                            <p class="text-muted">Already have an account? <a href="login.php" class="text-primary fw-bold">Sign In</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Form validation
document.getElementById('onboardingForm').addEventListener('submit', function(e) {
    const phone = document.getElementById('phone').value;
    const idNumber = document.getElementById('id_number').value;
    
    // Validate phone number (Kenyan format)
    const phoneRegex = /^[1-9]\d{8}$/;
    if (phone && !phoneRegex.test(phone)) {
        e.preventDefault();
        alert('Please enter a valid Kenyan phone number (10 digits without country code)');
        return;
    }
    
    // Validate ID number (8 digits)
    const idRegex = /^\d{8}$/;
    if (idNumber && !idRegex.test(idNumber)) {
        e.preventDefault();
        alert('Please enter a valid 8-digit National ID number');
        return;
    }
});

// Format phone number input
document.getElementById('phone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 9) {
        value = value.substring(0, 9);
    }
    e.target.value = value;
});

// Format ID number input
document.getElementById('id_number').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 8) {
        value = value.substring(0, 8);
    }
    e.target.value = value;
});
</script>
</body>
</html>