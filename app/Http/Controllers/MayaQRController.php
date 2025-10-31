<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use App\Events\PaymentReceived;
use Illuminate\Support\Str;

class MayaQRController extends Controller
{
    /**
     * Generate Maya QR code for an order
     * Called from tablet when customer selects Maya payment
     */
    public function generateQR(Request $request)
    {
        try {
            Log::info('Maya QR Generation Request', $request->all());

            // Validate request
            $validated = $request->validate([
                'order_type' => 'required|string',
                'table_number' => 'nullable|string',
                'items' => 'required|array',
                'items.*.menu_item_id' => 'required|integer',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.special_instructions' => 'nullable|string'
            ]);

            // Calculate total from cart items
            $subtotal = 0;
            $orderItems = [];

            foreach ($validated['items'] as $item) {
                $menuItem = \App\Models\MenuItem::find($item['menu_item_id']);
                
                if (!$menuItem) {
                    return response()->json([
                        'success' => false,
                        'message' => "Menu item not found: {$item['menu_item_id']}"
                    ], 404);
                }

                $itemTotal = $menuItem->price * $item['quantity'];
                $subtotal += $itemTotal;

                $orderItems[] = [
                    'menu_item_id' => $menuItem->id,
                    'name' => $menuItem->name,
                    'quantity' => $item['quantity'],
                    'price' => $menuItem->price,
                    'total_price' => $itemTotal,
                    'special_instructions' => $item['special_instructions'] ?? null
                ];
            }

            // Create order in database
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'order_type' => $validated['order_type'],
                'table_number' => $validated['table_number'] ?? null,
                'subtotal' => $subtotal,
                'total_amount' => $subtotal,
                'payment_method' => 'maya',
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'maya_payment_status' => 'pending',
                'created_at' => now(),
            ]);

            // Create order items
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['menu_item_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total_price' => $item['total_price'],
                    'special_instructions' => $item['special_instructions']
                ]);
            }

            // Call Maya API to create checkout
            $mayaResponse = $this->createMayaCheckout($order, $orderItems);

            if (!$mayaResponse['success']) {
                // Rollback order if Maya API fails
                $order->delete();
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create Maya checkout: ' . $mayaResponse['error']
                ], 500);
            }

            // Update order with Maya details
            $order->update([
                'maya_checkout_id' => $mayaResponse['checkoutId'],
                'maya_qr_url' => $mayaResponse['redirectUrl'],
                'maya_response_data' => json_encode($mayaResponse['fullResponse'])
            ]);

            Log::info('Maya QR Generated Successfully', [
                'order_id' => $order->id,
                'checkout_id' => $mayaResponse['checkoutId']
            ]);

            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'total_amount' => $order->total_amount,
                    'checkout_id' => $mayaResponse['checkoutId']
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Maya QR Validation Error', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Maya QR Generation Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while generating QR code'
            ], 500);
        }
    }

    /**
     * Display QR code page to customer
     */
    public function showQRPage($orderId)
    {
        try {
            $order = Order::with('orderItems.menuItem')->findOrFail($orderId);

            if ($order->payment_method !== 'maya') {
                return redirect()->route('kiosk.index')
                    ->with('error', 'Invalid payment method for this order');
            }

            if (!$order->maya_qr_url) {
                return redirect()->route('kiosk.index')
                    ->with('error', 'QR code not available for this order');
            }

            // Generate QR code using Endroid QR Code
            $result = Builder::create()
                ->writer(new PngWriter())
                ->writerOptions([])
                ->data($order->maya_qr_url)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(ErrorCorrectionLevel::High)
                ->size(300)
                ->margin(10)
                ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
                ->build();

            // Get the QR code as data URI
            $qrCodeDataUri = $result->getDataUri();

            return view('maya.qr-payment', [
                'order' => $order,
                'qrCodeDataUri' => $qrCodeDataUri,
                'checkoutUrl' => $order->maya_qr_url,
                'expiresIn' => 900 // 15 minutes in seconds
            ]);

        } catch (\Exception $e) {
            Log::error('Error displaying QR page', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('kiosk.index')
                ->with('error', 'Unable to display payment page');
        }
    }

    /**
     * Check payment status (called by polling from customer's page)
     */
    public function checkPaymentStatus($orderId)
    {
        try {
            $order = Order::findOrFail($orderId);

            return response()->json([
                'success' => true,
                'status' => $order->payment_status,
                'maya_status' => $order->maya_payment_status,
                'verified' => $order->payment_verified_at !== null,
                'order_status' => $order->order_status
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking payment status', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to check payment status'
            ], 500);
        }
    }

    /**
     * Webhook endpoint for Maya payment notifications
     */
    public function webhook(Request $request)
    {
        try {
            Log::info('Maya Webhook Received', $request->all());

            // Verify webhook signature
            $signature = $request->header('X-Maya-Signature');
            $webhookSecret = config('services.maya.webhook_secret');

            if ($signature && $webhookSecret) {
                $isValid = $this->verifyWebhookSignature(
                    $request->getContent(),
                    $signature,
                    $webhookSecret
                );

                if (!$isValid) {
                    Log::warning('Invalid Maya webhook signature');
                    return response()->json(['error' => 'Invalid signature'], 401);
                }
            }

            // Extract payment data
            $checkoutId = $request->input('id');
            $status = $request->input('status');
            $paymentStatus = $request->input('paymentStatus');
            
            // Find order by checkout ID
            $order = Order::where('maya_checkout_id', $checkoutId)->first();

            if (!$order) {
                Log::warning('Order not found for webhook', ['checkout_id' => $checkoutId]);
                return response()->json(['error' => 'Order not found'], 404);
            }

            // Update order based on payment status
            if ($status === 'COMPLETED' || $status === 'PAYMENT_SUCCESS' || $paymentStatus === 'PAYMENT_SUCCESS') {
                $order->update([
                    'payment_status' => 'paid',
                    'maya_payment_status' => 'completed',
                    'payment_verified_at' => now(),
                    'order_status' => 'confirmed'
                ]);

                Log::info('Payment successful via webhook', [
                    'order_id' => $order->id,
                    'checkout_id' => $checkoutId
                ]);

                // Broadcast event to notify cashier
                broadcast(new PaymentReceived($order))->toOthers();

                // Clear cart session if exists
                session()->forget('cart');

            } elseif ($status === 'PAYMENT_FAILED' || $status === 'EXPIRED' || $status === 'CANCELLED') {
                $order->update([
                    'payment_status' => 'failed',
                    'maya_payment_status' => strtolower($status),
                ]);

                Log::info('Payment failed/cancelled via webhook', [
                    'order_id' => $order->id,
                    'status' => $status
                ]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Maya Webhook Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Webhook processing failed'
            ], 500);
        }
    }

    /**
     * Create Maya checkout session via API
     */
    private function createMayaCheckout(Order $order, array $items)
    {
        try {
            $publicKey = config('services.maya.public_key');
            $secretKey = config('services.maya.secret_key');
            $apiUrl = config('services.maya.api_url');

            if (!$publicKey || !$secretKey) {
                return [
                    'success' => false,
                    'error' => 'Maya API credentials not configured'
                ];
            }

            // Prepare items for Maya
            $mayaItems = [];
            foreach ($items as $item) {
                $mayaItems[] = [
                    'name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'amount' => [
                        'value' => $item['price'],
                        'currency' => 'PHP'
                    ],
                    'totalAmount' => [
                        'value' => $item['total_price'],
                        'currency' => 'PHP'
                    ]
                ];
            }

            // Prepare checkout payload
            $payload = [
                'totalAmount' => [
                    'value' => $order->total_amount,
                    'currency' => 'PHP'
                ],
                'buyer' => [
                    'contact' => [
                        'phone' => '+639000000000', // You can collect this from customer
                        'email' => 'customer@example.com' // You can collect this from customer
                    ]
                ],
                'items' => $mayaItems,
                'requestReferenceNumber' => $order->order_number,
                'redirectUrl' => [
                    'success' => route('kiosk.paymentSuccess'),
                    'failure' => route('kiosk.paymentFailed'),
                    'cancel' => route('kiosk.main')
                ],
                'metadata' => [
                    'businessName' => 'L PRIMERO',
                    'orderId' => $order->id,
                    'orderType' => $order->order_type,
                    'tableNumber' => $order->table_number
                ]
            ];

            Log::info('Calling Maya API', ['payload' => $payload]);

            // Call Maya API
            $response = Http::withBasicAuth($publicKey, $secretKey)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->timeout(30)
                ->post("{$apiUrl}/checkout/v1/checkouts", $payload);

            if (!$response->successful()) {
                Log::error('Maya API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return [
                    'success' => false,
                    'error' => 'Maya API returned error: ' . $response->body()
                ];
            }

            $data = $response->json();

            Log::info('Maya API Success', ['response' => $data]);

            return [
                'success' => true,
                'checkoutId' => $data['checkoutId'],
                'redirectUrl' => $data['redirectUrl'],
                'fullResponse' => $data
            ];

        } catch (\Exception $e) {
            Log::error('Maya API Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify webhook signature from Maya
     */
    private function verifyWebhookSignature($payload, $signature, $secret)
    {
        // Maya uses HMAC SHA256 for webhook signatures
        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber()
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(4));
        
        return "{$prefix}-{$date}-{$random}";
    }
}