<?php
header('Content-Type: application/json');
session_start();

require_once '../classes/Subscription.php';
require_once '../classes/Payment.php';
require_once '../classes/Auth.php';

$response = ['success' => false, 'message' => ''];

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? $_GET['action'] ?? '';

    if (!Auth::isLoggedIn()) {
        throw new Exception('Authentication required');
    }

    $user = Auth::getCurrentUser();
    $subscriptionObj = new Subscription();
    $paymentObj = new Payment();

    switch ($action) {
        case 'subscribe':
            $planId = $input['plan_id'] ?? '';
            $paymentMethod = $input['payment_method'] ?? 'mpesa';
            $phoneNumber = $input['phone_number'] ?? '';

            if (empty($planId)) {
                throw new Exception('Plan ID is required');
            }

            $plan = $subscriptionObj->getPlanById($planId);
            if (!$plan) {
                throw new Exception('Invalid plan selected');
            }

            if ($plan['price'] > 0) {
                // Create payment record
                $paymentData = [
                    'user_id' => $user['id'],
                    'booking_id' => null,
                    'amount' => $plan['price'],
                    'currency' => 'KES',
                    'payment_method' => $paymentMethod,
                    'status' => 'pending',
                    'transaction_id' => 'SUB-' . $planId . '-' . time(),
                    'gateway_response' => null,
                    'description' => 'Subscription to ' . $plan['name']
                ];

                $paymentId = $paymentObj->createPayment($paymentData);

                if ($paymentMethod === 'mpesa' && !empty($phoneNumber)) {
                    // Process M-Pesa payment
                    $mpesaResult = $paymentObj->processMpesaPayment(
                        $phoneNumber,
                        $plan['price'],
                        'SUB-' . $planId,
                        'Subscription to ' . $plan['name']
                    );

                    if (isset($mpesaResult['ResponseCode']) && $mpesaResult['ResponseCode'] === '0') {
                        $response['success'] = true;
                        $response['message'] = 'M-Pesa payment initiated successfully';
                        $response['payment_id'] = $paymentId;
                    } else {
                        throw new Exception('M-Pesa payment failed: ' . ($mpesaResult['errorMessage'] ?? 'Unknown error'));
                    }
                } else {
                    // For card payments, redirect to payment gateway
                    $response['success'] = true;
                    $response['message'] = 'Payment initiated';
                    $response['payment_id'] = $paymentId;
                    $response['redirect_url'] = 'payment-gateway.php?payment_id=' . $paymentId;
                }
            } else {
                // Free plan
                $subscriptionId = $subscriptionObj->subscribeUser($user['id'], $planId, 'free');
                if ($subscriptionId) {
                    $response['success'] = true;
                    $response['message'] = 'Successfully subscribed to free plan';
                } else {
                    throw new Exception('Failed to create subscription');
                }
            }
            break;

        case 'cancel':
            $subscriptionId = $input['subscription_id'] ?? '';
            if (empty($subscriptionId)) {
                throw new Exception('Subscription ID is required');
            }

            // Cancel subscription logic here
            $response['success'] = true;
            $response['message'] = 'Subscription cancelled successfully';
            break;

        case 'usage':
            $usage = $subscriptionObj->getUsageStats($user['id']);
            $currentSubscription = $subscriptionObj->getUserSubscription($user['id']);
            
            $response['success'] = true;
            $response['usage'] = $usage;
            $response['subscription'] = $currentSubscription;
            break;

        default:
            throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
