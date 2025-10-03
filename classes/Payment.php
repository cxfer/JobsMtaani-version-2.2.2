<?php
/**
 * Payment Class
 * Handles payment processing, M-Pesa integration, and transaction management
 */

require_once __DIR__ . '/../config/database.php';

class Payment {
    private $conn;
    private $table_name = "payments";
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create new payment
    public function createPayment($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET user_id=:user_id, booking_id=:booking_id, amount=:amount, 
                      currency=:currency, payment_method=:payment_method, 
                      status=:status, transaction_id=:transaction_id, 
                      gateway_response=:gateway_response, description=:description";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":user_id", $data['user_id']);
        $stmt->bindParam(":booking_id", $data['booking_id']);
        $stmt->bindParam(":amount", $data['amount']);
        $stmt->bindParam(":currency", $data['currency']);
        $stmt->bindParam(":payment_method", $data['payment_method']);
        $stmt->bindParam(":status", $data['status']);
        $stmt->bindParam(":transaction_id", $data['transaction_id']);
        $stmt->bindParam(":gateway_response", $data['gateway_response']);
        $stmt->bindParam(":description", $data['description']);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Process M-Pesa payment
    public function processMpesaPayment($phoneNumber, $amount, $accountReference, $transactionDesc) {
        // M-Pesa STK Push implementation
        $consumerKey = $_ENV['MPESA_CONSUMER_KEY'] ?? '';
        $consumerSecret = $_ENV['MPESA_CONSUMER_SECRET'] ?? '';
        $businessShortCode = $_ENV['MPESA_SHORTCODE'] ?? '';
        $passkey = $_ENV['MPESA_PASSKEY'] ?? '';
        
        // Generate access token
        $credentials = base64_encode($consumerKey . ':' . $consumerSecret);
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($curl);
        $result = json_decode($response, true);
        curl_close($curl);
        
        if (!isset($result['access_token'])) {
            return ['success' => false, 'message' => 'Failed to get M-Pesa access token'];
        }
        
        $accessToken = $result['access_token'];
        
        // STK Push
        $timestamp = date('YmdHis');
        $password = base64_encode($businessShortCode . $passkey . $timestamp);
        
        $stkData = [
            'BusinessShortCode' => $businessShortCode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phoneNumber,
            'PartyB' => $businessShortCode,
            'PhoneNumber' => $phoneNumber,
            'CallBackURL' => $_ENV['MPESA_CALLBACK_URL'] ?? 'https://yourdomain.com/api/mpesa-callback.php',
            'AccountReference' => $accountReference,
            'TransactionDesc' => $transactionDesc
        ];
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($stkData));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($curl);
        $result = json_decode($response, true);
        curl_close($curl);
        
        return $result;
    }

    // Get payment by ID
    public function getPaymentById($id) {
        $query = "SELECT p.*, u.first_name, u.last_name, b.service_title 
                  FROM " . $this->table_name . " p
                  LEFT JOIN users u ON p.user_id = u.id
                  LEFT JOIN bookings b ON p.booking_id = b.id
                  WHERE p.id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    // Get user payments
    public function getUserPayments($userId, $limit = 20, $offset = 0) {
        $query = "SELECT p.*, b.service_title, b.booking_date 
                  FROM " . $this->table_name . " p
                  LEFT JOIN bookings b ON p.booking_id = b.id
                  WHERE p.user_id = :user_id
                  ORDER BY p.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update payment status
    public function updatePaymentStatus($paymentId, $status, $gatewayResponse = null) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = :status, gateway_response = :gateway_response, updated_at = NOW()
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':gateway_response', $gatewayResponse);
        $stmt->bindParam(':id', $paymentId);

        return $stmt->execute();
    }

    // Get payment statistics
    public function getPaymentStats($userId = null, $dateFrom = null, $dateTo = null) {
        $whereClause = "WHERE 1=1";
        $params = [];

        if ($userId) {
            $whereClause .= " AND user_id = :user_id";
            $params[':user_id'] = $userId;
        }

        if ($dateFrom) {
            $whereClause .= " AND created_at >= :date_from";
            $params[':date_from'] = $dateFrom;
        }

        if ($dateTo) {
            $whereClause .= " AND created_at <= :date_to";
            $params[':date_to'] = $dateTo;
        }

        $query = "SELECT 
                    COUNT(*) as total_payments,
                    SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as total_completed,
                    SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END) as total_pending,
                    SUM(CASE WHEN status = 'failed' THEN amount ELSE 0 END) as total_failed,
                    AVG(CASE WHEN status = 'completed' THEN amount ELSE NULL END) as avg_payment
                  FROM " . $this->table_name . " " . $whereClause;

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Generate payment receipt
    public function generateReceipt($paymentId) {
        $payment = $this->getPaymentById($paymentId);
        
        if (!$payment || $payment['status'] !== 'completed') {
            return false;
        }

        $receiptData = [
            'receipt_number' => 'RCP-' . str_pad($paymentId, 8, '0', STR_PAD_LEFT),
            'payment_id' => $paymentId,
            'amount' => $payment['amount'],
            'currency' => $payment['currency'],
            'payment_method' => $payment['payment_method'],
            'transaction_id' => $payment['transaction_id'],
            'customer_name' => $payment['first_name'] . ' ' . $payment['last_name'],
            'service_title' => $payment['service_title'],
            'payment_date' => $payment['created_at'],
            'status' => $payment['status']
        ];

        return $receiptData;
    }
}
?>
