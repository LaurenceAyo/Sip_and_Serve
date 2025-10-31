<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class MayaQRPaymentService
{
    protected $apiKey;
    protected $secretKey;
    protected $baseUrl;
    
    public function __construct()
    {
        $this->apiKey = env('MAYA_API_KEY');
        $this->secretKey = env('MAYA_SECRET_KEY');
        
        // Use sandbox for testing, production for live
        $this->baseUrl = env('MAYA_ENVIRONMENT', 'sandbox') === 'production'
            ? 'https://pg.paymaya.com'
            : 'https://pg-sandbox.paymaya.com';
    }
    
    /**
     * Create a QR Code for payment
     * 
     * @param array $orderData
     * @return array
     */
    public function createQRPayment($orderData)
    {
        try {
            // Generate unique reference number for this order
            $referenceNumber = 'ORD-' . str_pad($orderData['orderId'], 6, '0', STR_PAD_LEFT) . '-' . time();
            
            $payload = [
                'totalAmount' => [
                    'value' => (float) $orderData['amount'],
                    'currency' => 'PHP'
                ],
                'requestReferenceNumber' => $referenceNumber,
                'metadata' => [
                    'orderId' => $orderData['orderId'],
                    'orderType' => $orderData['orderType'] ?? 'dine-in',
                    'tableNumber' => $orderData['tableNumber'] ?? null,
                    'orderNumber' => $orderData['orderNumber'] ?? null
                ],
                // Webhook URL for payment notifications
                'redirectUrl' => [
                    'success' => route('maya.qr.webhook'),
                    'failure' => route('maya.qr.webhook'),
                    'cancel' => route('maya.qr.webhook')
                ]
            ];

            Log::info('Maya QR - Creating QR payment', [
                'order_id' => $orderData['orderId'],
                'amount' => $orderData['amount'],
                'reference' => $referenceNumber
            ]);

            // Create QR payment using Maya API
            $response = Http::withBasicAuth($this->apiKey, '')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($this->baseUrl . '/v1/payment-rrns', $payload);

            if ($response->successful()) {
                $result = $response->json();
                
                Log::info('Maya QR - QR payment created successfully', [
                    'order_id' => $orderData['orderId'],
                    'payment_id' => $result['id'] ?? null,
                    'qr_url' => $result['qrUrl'] ?? null
                ]);

                return [
                    'success' => true,
                    'paymentId' => $result['id'],
                    'qrUrl' => $result['qrUrl'], // URL to the QR code image
                    'referenceNumber' => $referenceNumber,
                    'expiresAt' => $result['expiresAt'] ?? null,
                    'response' => $result
                ];
            } else {
                Log::error('Maya QR - Failed to create QR payment', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [
                    'success' => false,
                    'error' => 'Failed to create QR payment: ' . $response->body()
                ];
            }
        } catch (Exception $e) {
            Log::error('Maya QR - Exception during QR creation', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check QR payment status
     * 
     * @param string $paymentId
     * @return array
     */
    public function getQRPaymentStatus($paymentId)
    {
        try {
            Log::info('Maya QR - Checking payment status', [
                'payment_id' => $paymentId
            ]);

            $response = Http::withBasicAuth($this->apiKey, '')
                ->get($this->baseUrl . "/v1/payment-rrns/{$paymentId}");

            if ($response->successful()) {
                $result = $response->json();
                
                return [
                    'success' => true,
                    'status' => $result['status'] ?? 'UNKNOWN',
                    'isPaid' => in_array($result['status'] ?? '', ['PAYMENT_SUCCESS', 'COMPLETED', 'SUCCESS']),
                    'data' => $result
                ];
            } else {
                Log::error('Maya QR - Failed to retrieve status', [
                    'payment_id' => $paymentId,
                    'status' => $response->status()
                ]);

                return [
                    'success' => false,
                    'error' => 'Failed to retrieve status'
                ];
            }
        } catch (Exception $e) {
            Log::error('Maya QR - Exception retrieving status', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Handle webhook notification from Maya for QR payments
     * 
     * @param array $payload
     * @return array
     */
    public function handleQRWebhook($payload)
    {
        try {
            Log::info('Maya QR - Webhook received', [
                'payload' => $payload
            ]);

            $paymentId = $payload['id'] ?? null;
            $status = $payload['status'] ?? 'UNKNOWN';
            $referenceNumber = $payload['requestReferenceNumber'] ?? null;

            // Extract order ID from reference number
            // Format: ORD-000001-1234567890
            $orderId = null;
            if ($referenceNumber && preg_match('/ORD-(\d+)-/', $referenceNumber, $matches)) {
                $orderId = (int) $matches[1];
            }

            // Alternatively, check metadata
            if (!$orderId && isset($payload['metadata']['orderId'])) {
                $orderId = $payload['metadata']['orderId'];
            }

            if (!$orderId) {
                throw new Exception('Order ID not found in webhook payload');
            }

            return [
                'success' => true,
                'paymentId' => $paymentId,
                'status' => $status,
                'orderId' => $orderId,
                'isPaid' => in_array($status, ['PAYMENT_SUCCESS', 'COMPLETED', 'SUCCESS']),
                'payload' => $payload
            ];
        } catch (Exception $e) {
            Log::error('Maya QR - Webhook processing error', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify QR payment before fulfilling order
     * 
     * @param string $paymentId
     * @return bool
     */
    public function verifyQRPayment($paymentId)
    {
        $status = $this->getQRPaymentStatus($paymentId);
        
        if ($status['success'] && isset($status['isPaid'])) {
            return $status['isPaid'];
        }
        
        return false;
    }

    /**
     * Generate a simple verification code for customer
     * This is shown to customer and cashier for easy matching
     * 
     * @param int $orderId
     * @return string
     */
    public function generateVerificationCode($orderId)
    {
        // Generate a 6-digit code based on order ID and timestamp
        $timestamp = substr(time(), -4);
        $orderDigits = str_pad($orderId % 100, 2, '0', STR_PAD_LEFT);
        $random = substr(md5($orderId . $timestamp), 0, 2);
        
        return strtoupper($orderDigits . $random . $timestamp);
    }
}