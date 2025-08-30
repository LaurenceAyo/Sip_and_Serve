<?php

namespace App\Http\Controllers;

use App\Services\PaymongoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class POSPaymentController extends Controller
{
    protected $paymongoService;

    public function __construct(PaymongoService $paymongoService)
    {
        $this->paymongoService = $paymongoService;
    }

    /**
     * Process POS payment with GCash
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'order_id' => 'required|string',
            'customer_name' => 'nullable|string',
            'payment_method' => 'required|in:gcash',
        ]);

        DB::beginTransaction();
        try {
            // Create payment intent
            $paymentIntent = $this->paymongoService->createPaymentIntent(
                $request->amount,
                'PHP',
                "POS Order #{$request->order_id} - {$request->customer_name}"
            );

            if (!$paymentIntent || !isset($paymentIntent['data'])) {
                throw new \Exception('Failed to create payment intent with PayMongo');
            }

            $paymentIntentId = $paymentIntent['data']['id'];

            // Create payment method (GCash)
            $paymentMethod = $this->paymongoService->createPaymentMethod($paymentIntentId, 'gcash');

            if (!$paymentMethod || !isset($paymentMethod['data'])) {
                throw new \Exception('Failed to create payment method');
            }

            $paymentMethodId = $paymentMethod['data']['id'];

            // Attach payment method to payment intent
            $attachResult = $this->paymongoService->attachPaymentMethod($paymentIntentId, $paymentMethodId);

            if (!$attachResult || !isset($attachResult['data'])) {
                throw new \Exception('Failed to attach payment method to payment intent');
            }

            // Store payment record in database
            $paymentRecord = $this->storePaymentRecord([
                'order_id' => $request->order_id,
                'payment_intent_id' => $paymentIntentId,
                'payment_method_id' => $paymentMethodId,
                'amount' => $request->amount,
                'customer_name' => $request->customer_name,
                'status' => $attachResult['data']['attributes']['status'],
                'payment_method_type' => 'gcash'
            ]);

            DB::commit();

            // Get the redirect URL for GCash payment
            $redirectUrl = null;
            if (isset($attachResult['data']['attributes']['next_action']['redirect']['url'])) {
                $redirectUrl = $attachResult['data']['attributes']['next_action']['redirect']['url'];
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment intent created successfully',
                'data' => [
                    'payment_intent_id' => $paymentIntentId,
                    'payment_method_id' => $paymentMethodId,
                    'status' => $attachResult['data']['attributes']['status'],
                    'redirect_url' => $redirectUrl,
                    'payment_record_id' => $paymentRecord
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('POS Payment Processing Error', [
                'message' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Check payment status
     */
    public function checkPaymentStatus($paymentIntentId)
    {
        try {
            $paymentIntent = $this->paymongoService->retrievePaymentIntent($paymentIntentId);

            if (!$paymentIntent || !isset($paymentIntent['data'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment intent not found'
                ], 404);
            }

            $status = $paymentIntent['data']['attributes']['status'];
            
            // Update local database
            $this->updatePaymentStatus($paymentIntentId, $status);

            return response()->json([
                'success' => true,
                'status' => $status,
                'data' => $paymentIntent['data']
            ]);

        } catch (\Exception $e) {
            Log::error('Payment Status Check Error', [
                'payment_intent_id' => $paymentIntentId,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check payment status'
            ], 500);
        }
    }

    /**
     * Handle payment success redirect
     */
    public function paymentSuccess(Request $request)
    {
        $paymentIntentId = $request->query('payment_intent_id');
        
        if ($paymentIntentId) {
            // Verify payment status
            $paymentIntent = $this->paymongoService->retrievePaymentIntent($paymentIntentId);
            
            if ($paymentIntent && $paymentIntent['data']['attributes']['status'] === 'succeeded') {
                $this->updatePaymentStatus($paymentIntentId, 'succeeded');
                
                return view('kiosk.payment-success', [
                    'payment_intent_id' => $paymentIntentId,
                    'message' => 'Payment completed successfully!'
                ]);
            }
        }

        return view('kiosk.payment-failed', [
            'message' => 'Payment verification failed'
        ]);
    }

    /**
     * Handle payment failure redirect
     */
    public function paymentFailed(Request $request)
    {
        $paymentIntentId = $request->query('payment_intent_id');
        
        if ($paymentIntentId) {
            $this->updatePaymentStatus($paymentIntentId, 'failed');
        }

        return view('kiosk.payment-failed', [
            'message' => 'Payment was not completed'
        ]);
    }

    /**
     * Handle PayMongo webhooks
     */
    public function handleWebhook(Request $request)
    {
        try {
            $payload = $request->all();
            
            Log::info('PayMongo Webhook Received', ['payload' => $payload]);

            if (!isset($payload['data']['attributes']['type'])) {
                return response()->json(['message' => 'Invalid webhook payload'], 400);
            }

            $eventType = $payload['data']['attributes']['type'];
            
            switch ($eventType) {
                case 'payment_intent.succeeded':
                    $this->handlePaymentSucceeded($payload);
                    break;
                case 'payment_intent.payment_failed':
                    $this->handlePaymentFailed($payload);
                    break;
            }

            return response()->json(['message' => 'Webhook processed successfully']);

        } catch (\Exception $e) {
            Log::error('Webhook Processing Error', [
                'message' => $e->getMessage(),
                'payload' => $request->all()
            ]);

            return response()->json(['message' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Store payment record in database
     */
    private function storePaymentRecord($data)
    {
        return DB::table('pos_payments')->insertGetId([
            'order_id' => $data['order_id'],
            'payment_intent_id' => $data['payment_intent_id'],
            'payment_method_id' => $data['payment_method_id'],
            'amount' => $data['amount'],
            'customer_name' => $data['customer_name'],
            'status' => $data['status'],
            'payment_method_type' => $data['payment_method_type'],
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Update payment status in database
     */
    private function updatePaymentStatus($paymentIntentId, $status)
    {
        DB::table('pos_payments')
            ->where('payment_intent_id', $paymentIntentId)
            ->update([
                'status' => $status,
                'updated_at' => now()
            ]);
    }

    /**
     * Handle successful payment webhook
     */
    private function handlePaymentSucceeded($payload)
    {
        $paymentIntentId = $payload['data']['attributes']['data']['id'];
        $this->updatePaymentStatus($paymentIntentId, 'succeeded');
        
        Log::info('Payment Succeeded', ['payment_intent_id' => $paymentIntentId]);
    }

    /**
     * Handle failed payment webhook
     */
    private function handlePaymentFailed($payload)
    {
        $paymentIntentId = $payload['data']['attributes']['data']['id'];
        $this->updatePaymentStatus($paymentIntentId, 'failed');
        
        Log::info('Payment Failed', ['payment_intent_id' => $paymentIntentId]);
    }
}