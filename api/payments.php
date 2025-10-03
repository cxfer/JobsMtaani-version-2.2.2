<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Auth.php';

header('Content-Type: application/json');

// Require authentication
Auth::requireAuth();

$database = new Database();
$db = $database->getConnection();
$current_user = Auth::getCurrentUser();

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        if (isset($_GET['stats'])) {
            // Get payment statistics
            $stmt = $db->prepare("
                SELECT 
                    SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as total_paid,
                    SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END) as pending_payments,
                    SUM(CASE WHEN status = 'completed' AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW()) THEN amount ELSE 0 END) as this_month_spent,
                    COUNT(*) as total_transactions
                FROM payments p
                JOIN bookings b ON p.booking_id = b.id
                WHERE b.customer_id = ?
            ");
            $stmt->execute([$current_user['id']]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'data' => $stats]);
        } else {
            // Get payment history with filters
            $where_conditions = ["b.customer_id = ?"];
            $params = [$current_user['id']];
            
            if (isset($_GET['status']) && !empty($_GET['status'])) {
                $where_conditions[] = "p.status = ?";
                $params[] = $_GET['status'];
            }
            
            if (isset($_GET['month']) && !empty($_GET['month'])) {
                $where_conditions[] = "DATE_FORMAT(p.created_at, '%Y-%m') = ?";
                $params[] = $_GET['month'];
            }
            
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $where_conditions[] = "(s.title LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR p.transaction_id LIKE ?)";
                $search_term = '%' . $_GET['search'] . '%';
                $params = array_merge($params, [$search_term, $search_term, $search_term, $search_term]);
            }
            
            $where_clause = implode(' AND ', $where_conditions);
            
            $stmt = $db->prepare("
                SELECT p.*, s.title as service_name, s.price,
                       u.first_name, u.last_name,
                       b.booking_date, b.total_amount
                FROM payments p
                JOIN bookings b ON p.booking_id = b.id
                JOIN services s ON b.service_id = s.id
                JOIN users u ON s.provider_id = u.id
                WHERE {$where_clause}
                ORDER BY p.created_at DESC
                LIMIT 50
            ");
            $stmt->execute($params);
            $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'data' => $payments]);
        }
        break;
        
    case 'GET':
        if (isset($_GET['receipt']) && isset($_GET['payment_id'])) {
            // Get payment receipt details
            $stmt = $db->prepare("
                SELECT p.*, s.title as service_name, s.description,
                       u.first_name as provider_first_name, u.last_name as provider_last_name,
                       c.first_name as customer_first_name, c.last_name as customer_last_name,
                       b.booking_date, b.service_address, b.total_amount
                FROM payments p
                JOIN bookings b ON p.booking_id = b.id
                JOIN services s ON b.service_id = s.id
                JOIN users u ON s.provider_id = u.id
                JOIN users c ON b.customer_id = c.id
                WHERE p.id = ? AND b.customer_id = ?
            ");
            $stmt->execute([$_GET['payment_id'], $current_user['id']]);
            $receipt = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($receipt) {
                echo json_encode(['success' => true, 'data' => $receipt]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Receipt not found']);
            }
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}
?>
