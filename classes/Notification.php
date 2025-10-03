<?php
/**
 * Notification Management Class
 * Handles real-time notifications, email notifications, and SMS notifications
 */

class Notification {
    private $conn;
    private $table_name = "notifications";
    
    public $id;
    public $user_id;
    public $title;
    public $message;
    public $type;
    public $data;
    public $is_read;
    public $created_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Create a new notification
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id = :user_id,
                      title = :title,
                      message = :message,
                      type = :type,
                      data = :data,
                      is_read = 0,
                      created_at = NOW()";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize inputs
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->message = htmlspecialchars(strip_tags($this->message));
        $this->type = htmlspecialchars(strip_tags($this->type));
        $this->data = json_encode($this->data);
        
        // Bind parameters
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":message", $this->message);
        $stmt->bindParam(":type", $this->type);
        $stmt->bindParam(":data", $this->data);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            
            // Send real-time notification via WebSocket
            $this->sendRealTimeNotification();
            
            // Send email notification if enabled
            if ($this->shouldSendEmail()) {
                $this->sendEmailNotification();
            }
            
            // Send SMS notification if enabled
            if ($this->shouldSendSMS()) {
                $this->sendSMSNotification();
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Get notifications for a user
     */
    public function getUserNotifications($user_id, $limit = 20, $offset = 0) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE user_id = :user_id 
                  ORDER BY created_at DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($notification_id, $user_id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET is_read = 1 
                  WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $notification_id);
        $stmt->bindParam(":user_id", $user_id);
        
        return $stmt->execute();
    }
    
    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($user_id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET is_read = 1 
                  WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        
        return $stmt->execute();
    }
    
    /**
     * Get unread notification count
     */
    public function getUnreadCount($user_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                  WHERE user_id = :user_id AND is_read = 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
    
    /**
     * Send real-time notification via WebSocket
     */
    private function sendRealTimeNotification() {
        // WebSocket implementation would go here
        // For now, we'll use Server-Sent Events (SSE) as a simpler alternative
        
        $notification_data = [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'data' => json_decode($this->data, true),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Store in session for SSE pickup
        if (!isset($_SESSION['pending_notifications'])) {
            $_SESSION['pending_notifications'] = [];
        }
        
        $_SESSION['pending_notifications'][] = $notification_data;
    }
    
    /**
     * Check if email notification should be sent
     */
    private function shouldSendEmail() {
        // Check user preferences and notification type
        $query = "SELECT email_notifications FROM users WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user && $user['email_notifications'] == 1;
    }
    
    /**
     * Check if SMS notification should be sent
     */
    private function shouldSendSMS() {
        // Check user preferences and notification type
        $query = "SELECT sms_notifications FROM users WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user && $user['sms_notifications'] == 1;
    }
    
    /**
     * Send email notification
     */
    private function sendEmailNotification() {
        // Get user email
        $query = "SELECT email, first_name FROM users WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $to = $user['email'];
            $subject = $this->title;
            $message = "
                <html>
                <head>
                    <title>{$this->title}</title>
                </head>
                <body>
                    <h2>Hello {$user['first_name']},</h2>
                    <p>{$this->message}</p>
                    <p>Best regards,<br>JobsMtaani Team</p>
                </body>
                </html>
            ";
            
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: noreply@jobsmtaani.com" . "\r\n";
            
            mail($to, $subject, $message, $headers);
        }
    }
    
    /**
     * Send SMS notification
     */
    private function sendSMSNotification() {
        // Get user phone
        $query = "SELECT phone, first_name FROM users WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && $user['phone']) {
            // SMS implementation would go here
            // This could integrate with services like Twilio, Africa's Talking, etc.
            $sms_message = "JobsMtaani: " . $this->message;
            // $this->sendSMS($user['phone'], $sms_message);
        }
    }
    
    /**
     * Create notification for booking events
     */
    public static function createBookingNotification($db, $booking_id, $type) {
        $notification = new self($db);
        
        // Get booking details
        $query = "SELECT b.*, s.title as service_title, 
                         c.first_name as customer_name, c.id as customer_id,
                         p.first_name as provider_name, p.id as provider_id
                  FROM bookings b
                  JOIN services s ON b.service_id = s.id
                  JOIN users c ON b.customer_id = c.id
                  JOIN users p ON s.provider_id = p.id
                  WHERE b.id = :booking_id";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":booking_id", $booking_id);
        $stmt->execute();
        
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($booking) {
            switch ($type) {
                case 'booking_created':
                    // Notify provider
                    $notification->user_id = $booking['provider_id'];
                    $notification->title = "New Booking Request";
                    $notification->message = "You have a new booking request for {$booking['service_title']} from {$booking['customer_name']}.";
                    $notification->type = "booking";
                    $notification->data = ['booking_id' => $booking_id, 'action' => 'view_booking'];
                    $notification->create();
                    break;
                    
                case 'booking_confirmed':
                    // Notify customer
                    $notification->user_id = $booking['customer_id'];
                    $notification->title = "Booking Confirmed";
                    $notification->message = "Your booking for {$booking['service_title']} has been confirmed by {$booking['provider_name']}.";
                    $notification->type = "booking";
                    $notification->data = ['booking_id' => $booking_id, 'action' => 'view_booking'];
                    $notification->create();
                    break;
                    
                case 'booking_completed':
                    // Notify customer
                    $notification->user_id = $booking['customer_id'];
                    $notification->title = "Service Completed";
                    $notification->message = "Your booking for {$booking['service_title']} has been completed. Please leave a review.";
                    $notification->type = "booking";
                    $notification->data = ['booking_id' => $booking_id, 'action' => 'leave_review'];
                    $notification->create();
                    break;
            }
        }
    }
    
    /**
     * Create notification for payment events
     */
    public static function createPaymentNotification($db, $payment_id, $type) {
        $notification = new self($db);
        
        // Get payment details
        $query = "SELECT p.*, b.booking_number, s.title as service_title,
                         u.first_name, u.id as user_id
                  FROM payments p
                  JOIN bookings b ON p.booking_id = b.id
                  JOIN services s ON b.service_id = s.id
                  JOIN users u ON b.customer_id = u.id
                  WHERE p.id = :payment_id";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":payment_id", $payment_id);
        $stmt->execute();
        
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($payment) {
            switch ($type) {
                case 'payment_successful':
                    $notification->user_id = $payment['user_id'];
                    $notification->title = "Payment Successful";
                    $notification->message = "Your payment of KES " . number_format($payment['amount']) . " for {$payment['service_title']} was successful.";
                    $notification->type = "payment";
                    $notification->data = ['payment_id' => $payment_id, 'booking_id' => $payment['booking_id']];
                    $notification->create();
                    break;
                    
                case 'payment_failed':
                    $notification->user_id = $payment['user_id'];
                    $notification->title = "Payment Failed";
                    $notification->message = "Your payment for {$payment['service_title']} failed. Please try again.";
                    $notification->type = "payment";
                    $notification->data = ['payment_id' => $payment_id, 'booking_id' => $payment['booking_id']];
                    $notification->create();
                    break;
            }
        }
    }
}
?>
