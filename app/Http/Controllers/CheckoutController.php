<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller 
{
    public function processCheckout(Request $request)
    {
        try {
            DB::beginTransaction();
            
            // 1. FIRST - Create the order with pending status
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'order_type' => $request->order_type, // dine-in, takeout, etc.
                'table_number' => $request->table_number,
                'subtotal' => $request->subtotal,
                'tax_amount' => $request->tax_amount,
                'total_amount' => $request->total_amount,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending', // Important: set as pending
                'status' => 'pending',
                'created_at' => now(),
            ]);

            // Add order items
            foreach ($request->items as $item) {
                $order->orderItems()->create([
                    'menu_item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            Log::info('Order created before payment', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount
            ]);

            // 2. SECOND - Create payment intent with order reference
            $paymentIntent = $this->createPaymentIntent($order);
            
            // 3. THIRD - Update order with payment intent ID
            $order->update(['payment_intent_id' => $paymentIntent['id']]);

            Log::info('Payment intent created and linked to order', [
                'order_id' => $order->id,
                'payment_intent_id' => $paymentIntent['id']
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'payment_intent' => $paymentIntent
            ]);

        } catch (Exception $e) {
            DB::rollback();
            
            Log::error('Checkout process failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Checkout failed. Please try again.'
            ], 500);
        }
    }

    private function createPaymentIntent($order)
    {
        $secretKey = config('paymongo.secret_key');
        
        // Convert peso to cents (PayMongo uses cents)
        $amountInCents = $order->total_amount * 100;

        $paymentIntentData = [
            'data' => [
                'attributes' => [
                    'amount' => $amountInCents,
                    'payment_method_allowed' => ['gcash'],
                    'payment_method_options' => [
                        'gcash' => [
                            'redirect' => [
                                'success' => url('/payment/success'),
                                'failed' => url('/payment/failed')
                            ]
                        ]
                    ],
                    'currency' => 'PHP',
                    'description' => 'Order #' . $order->order_number,
                    'statement_descriptor' => 'Sip & Serve',
                    // IMPORTANT: Add order reference in metadata
                    'metadata' => [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number
                    ]
                ]
            ]
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($secretKey . ':'),
            'Content-Type' => 'application/json',
        ])->post('https://api.paymongo.com/v1/payment_intents', $paymentIntentData);

        if ($response->successful()) {
            return $response->json()['data'];
        }

        throw new Exception('Failed to create payment intent: ' . $response->body());
    }

    private function generateOrderNumber()
    {
        // Generate unique order number
        $prefix = 'ORD';
        $timestamp = now()->format('ymdHis');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        return $prefix . $timestamp . $random;
    }
}