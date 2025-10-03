<?php
/**
 * Subscription Class
 * Handles subscription plans, billing cycles, and plan management
 */

require_once __DIR__ . '/../config/database.php';

class Subscription {
    private $conn;
    private $plans_table = "subscription_plans";
    private $subscriptions_table = "user_subscriptions";
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Get all subscription plans
    public function getPlans() {
        $query = "SELECT * FROM " . $this->plans_table . " WHERE status = 'active' ORDER BY price ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get plan by ID
    public function getPlanById($planId) {
        $query = "SELECT * FROM " . $this->plans_table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $planId);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    // Get user's current subscription
    public function getUserSubscription($userId) {
        $query = "SELECT us.*, sp.name as plan_name, sp.price, sp.features, sp.billing_cycle
                  FROM " . $this->subscriptions_table . " us
                  JOIN " . $this->plans_table . " sp ON us.plan_id = sp.id
                  WHERE us.user_id = :user_id AND us.status = 'active'
                  ORDER BY us.created_at DESC LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    // Subscribe user to plan
    public function subscribeUser($userId, $planId, $paymentMethod = 'free') {
        $plan = $this->getPlanById($planId);
        if (!$plan) {
            return false;
        }

        // Calculate next billing date
        $nextBilling = $this->calculateNextBilling($plan['billing_cycle']);

        // Deactivate current subscription
        $this->deactivateUserSubscriptions($userId);

        // Create new subscription
        $query = "INSERT INTO " . $this->subscriptions_table . " 
                  SET user_id=:user_id, plan_id=:plan_id, status='active', 
                      next_billing_date=:next_billing, payment_method=:payment_method";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':plan_id', $planId);
        $stmt->bindParam(':next_billing', $nextBilling);
        $stmt->bindParam(':payment_method', $paymentMethod);

        if($stmt->execute()) {
            // Update user's subscription plan
            $this->updateUserPlan($userId, $plan['slug']);
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Calculate next billing date
    private function calculateNextBilling($billingCycle) {
        $now = new DateTime();
        
        switch($billingCycle) {
            case 'monthly':
                $now->add(new DateInterval('P1M'));
                break;
            case 'quarterly':
                $now->add(new DateInterval('P3M'));
                break;
            case 'yearly':
                $now->add(new DateInterval('P1Y'));
                break;
            case 'lifetime':
                $now->add(new DateInterval('P50Y')); // 50 years for lifetime
                break;
            default:
                $now->add(new DateInterval('P1M'));
        }
        
        return $now->format('Y-m-d H:i:s');
    }

    // Deactivate user subscriptions
    private function deactivateUserSubscriptions($userId) {
        $query = "UPDATE " . $this->subscriptions_table . " 
                  SET status='cancelled', cancelled_at=NOW() 
                  WHERE user_id=:user_id AND status='active'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }

    // Update user plan in users table
    private function updateUserPlan($userId, $planSlug) {
        $query = "UPDATE users SET subscription_plan=:plan WHERE id=:user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':plan', $planSlug);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }

    // Check if user has feature access
    public function hasFeatureAccess($userId, $feature) {
        $subscription = $this->getUserSubscription($userId);
        
        if (!$subscription) {
            // Default free plan features
            $freeFeatures = ['basic_listing', 'customer_support'];
            return in_array($feature, $freeFeatures);
        }

        $features = json_decode($subscription['features'], true);
        return in_array($feature, $features);
    }

    // Get subscription usage stats
    public function getUsageStats($userId) {
        $query = "SELECT 
                    COUNT(CASE WHEN s.status = 'active' THEN 1 END) as active_services,
                    COUNT(CASE WHEN b.status = 'completed' THEN 1 END) as completed_bookings,
                    COUNT(CASE WHEN b.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH) THEN 1 END) as monthly_bookings
                  FROM users u
                  LEFT JOIN services s ON u.id = s.provider_id
                  LEFT JOIN bookings b ON u.id = b.customer_id OR u.id = b.provider_id
                  WHERE u.id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Process subscription renewal
    public function processRenewal($subscriptionId) {
        $query = "SELECT us.*, sp.price, sp.billing_cycle, u.id as user_id
                  FROM " . $this->subscriptions_table . " us
                  JOIN " . $this->plans_table . " sp ON us.plan_id = sp.id
                  JOIN users u ON us.user_id = u.id
                  WHERE us.id = :subscription_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':subscription_id', $subscriptionId);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $subscription = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Create payment record for renewal
            $paymentData = [
                'user_id' => $subscription['user_id'],
                'booking_id' => null,
                'amount' => $subscription['price'],
                'currency' => 'KES',
                'payment_method' => $subscription['payment_method'],
                'status' => 'pending',
                'transaction_id' => 'SUB-' . $subscriptionId . '-' . time(),
                'gateway_response' => null,
                'description' => 'Subscription renewal'
            ];

            $paymentObj = new Payment();
            $paymentId = $paymentObj->createPayment($paymentData);

            if ($paymentId) {
                // Update next billing date
                $nextBilling = $this->calculateNextBilling($subscription['billing_cycle']);
                $updateQuery = "UPDATE " . $this->subscriptions_table . " 
                               SET next_billing_date=:next_billing WHERE id=:id";
                
                $updateStmt = $this->conn->prepare($updateQuery);
                $updateStmt->bindParam(':next_billing', $nextBilling);
                $updateStmt->bindParam(':id', $subscriptionId);
                $updateStmt->execute();

                return $paymentId;
            }
        }
        return false;
    }
}
?>
