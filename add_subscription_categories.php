<?php
/**
 * Add Subscription-Based Categories Script
 * This script adds premium subscription-based categories to the service_categories table
 */

require_once 'config/database.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Add Subscription Categories</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
<div class='container py-5'>
    <div class='row justify-content-center'>
        <div class='col-md-8'>
            <div class='card'>
                <div class='card-header bg-primary text-white'>
                    <h3 class='mb-0'>Add Subscription-Based Categories</h3>
                </div>
                <div class='card-body'>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("Failed to connect to database");
    }
    
    echo "<div class='alert alert-success'>✓ Database connection established</div>";
    
    // Subscription-based categories to add
    $subscriptionCategories = [
        [
            'name' => 'Premium Home Services',
            'slug' => 'premium-home-services',
            'description' => 'High-end home maintenance and improvement services for premium subscribers',
            'icon' => 'fas fa-home',
            'sort_order' => 9,
            'is_premium' => 1
        ],
        [
            'name' => 'Luxury Beauty & Wellness',
            'slug' => 'luxury-beauty-wellness',
            'description' => 'Exclusive beauty treatments and wellness services for premium members',
            'icon' => 'fas fa-spa',
            'sort_order' => 10,
            'is_premium' => 1
        ],
        [
            'name' => 'Executive Transportation',
            'slug' => 'executive-transportation',
            'description' => 'Premium transportation services including chauffeur and luxury vehicle options',
            'icon' => 'fas fa-car-side',
            'sort_order' => 11,
            'is_premium' => 1
        ],
        [
            'name' => 'Professional Consultation',
            'slug' => 'professional-consultation',
            'description' => 'Expert consultation services in business, finance, and legal matters',
            'icon' => 'fas fa-briefcase',
            'sort_order' => 12,
            'is_premium' => 1
        ],
        [
            'name' => 'Elite Events & Catering',
            'slug' => 'elite-events-catering',
            'description' => 'High-end event planning and catering services for special occasions',
            'icon' => 'fas fa-glass-cheers',
            'sort_order' => 13,
            'is_premium' => 1
        ]
    ];
    
    $addedCategories = 0;
    
    foreach ($subscriptionCategories as $category) {
        // Check if category already exists
        $stmt = $db->prepare("SELECT id FROM service_categories WHERE slug = ?");
        $stmt->execute([$category['slug']]);
        
        if ($stmt->rowCount() > 0) {
            echo "<div class='alert alert-warning'>⚠ Category '{$category['name']}' already exists</div>";
            continue;
        }
        
        // Add new category
        $stmt = $db->prepare("INSERT INTO service_categories (name, slug, description, icon, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([
            $category['name'],
            $category['slug'],
            $category['description'],
            $category['icon'],
            $category['sort_order'],
            1 // is_active
        ]);
        
        if ($result) {
            echo "<div class='alert alert-success'>✓ Added category: {$category['name']}</div>";
            $addedCategories++;
        } else {
            echo "<div class='alert alert-danger'>✗ Failed to add category: {$category['name']}</div>";
        }
    }
    
    echo "<div class='alert alert-info mt-4'>
            <h5>Subscription Categories Summary</h5>
            <p>Added $addedCategories new subscription-based categories to the platform.</p>
            <p>These premium categories will be available to users with active subscriptions.</p>
          </div>";
    
    // Display all categories
    echo "<h5 class='mt-4'>Current Categories</h5>";
    $stmt = $db->prepare("SELECT name, slug, description FROM service_categories ORDER BY sort_order");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div class='table-responsive'>
            <table class='table table-striped'>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>";
    
    foreach ($categories as $category) {
        echo "<tr>
                <td>{$category['name']}</td>
                <td>{$category['slug']}</td>
                <td>{$category['description']}</td>
              </tr>";
    }
    
    echo "      </tbody>
            </table>
          </div>";
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
}

echo "                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>";

?>