<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymongoWebhookController extends Controller
{
    /**
     * Handle PayMongo webhook events
     */
    public function handleWebhook(Request $request)
    {
        try {
            // Get the raw payload
            $payload = $request->getContent();
            $signature = $request->header('Paymongo-Signature');
            
            // Log incoming webhook for debugging
            Log::info('PayMongo Webhook Received', [
                'headers' => $request->headers->all(),
                'payload_preview' => substr($payload, 0, 200),
                'signature' => $signature
            ]);

            // Verify webhook signature if webhook secret is configured
            if (config('paymongo.webhook_secret')) {
                if (!$this->verifyWebhookSignature($payload, $signature)) {
                    Log::warning('PayMongo Webhook: Invalid signature', [
                        'expected_signature_start' => substr(hash_hmac('sha256', $payload, config('paymongo.webhook_secret')), 0, 20),
                        'received_signature' => $signature
                    ]);
                    return response('Invalid signature', 400);
                }
            }

            // Parse the event data
            $event = json_decode($payload, true);
            
            if (!$event) {
                Log::error('PayMongo Webhook: Invalid JSON payload');
                return response('Invalid JSON', 400);
            }

            // Log the full event for debugging
            Log::info('PayMongo Webhook Event', [
                'event_type' => $event['data']['type'] ?? 'unknown',
                'event_id' => $event['data']['id'] ?? 'unknown',
                'event_data' => $event
            ]);

            // Handle different webhook events
            $eventType = $event['data']['type'] ?? null;
            
            switch ($eventType) {
                case 'payment_intent.payment.paid':
                    return $this->handlePaymentPaid($event);
                    
                case 'payment_intent.payment.failed':
                    return $this->handlePaymentFailed($event);
                    
                case 'payment_intent.payment.processing':
                    return $this->handlePaymentProcessing($event);
                    
                case 'payment_method.updated':
                    return $this->handlePaymentMethodUpdated($event);
                    
                default:
                    Log::info('PayMongo Webhook: Unhandled event type', [
                        'event_type' => $eventType
                    ]);
                    return response('Event type not handled', 200);
            }

        } catch (\Exception $e) {
            Log::error('PayMongo Webhook: Processing error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->getContent()
            ]);
            
            return response('Processing error', 500);
        }
    }

    /**
     * Handle successful payment webhook
     */
    private function handlePaymentPaid($event)
    {
        try {
            // Extract payment intent ID from the webhook data
            $paymentIntentId = $event['data']['attributes']['data']['id'] ?? null;
            
            if (!$paymentIntentId) {
                Log::warning('PayMongo Webhook: No payment intent ID in paid event');
                return response('No payment intent ID', 400);
            }

            // Find the order by payment intent ID
            $order = Order::where('paymongo_payment_intent_id', $paymentIntentId)->first();
            
            if (!$order) {
                Log::warning('PayMongo Webhook: Order not found for payment intent', [
                    'payment_intent_id' => $paymentIntentId
                ]);
                return response('Order not found', 404);
            }

            // Check if order is already marked as paid
            if ($order->payment_status === 'paid') {
                Log::info('PayMongo Webhook: Order already marked as paid', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number
                ]);
                return response('Order already paid', 200);
            }

            // Update order status using database transaction
            DB::transaction(function () use ($order, $event) {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'preparing',
                    'paid_at' => now(),
                ]);

                Log::info('PayMongo Webhook: Order marked as paid', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'total_amount' => $order->total_amount,
                    'payment_intent_id' => $order->paymongo_payment_intent_id
                ]);
            });

            return response('Payment processed successfully', 200);

        } catch (\Exception $e) {
            Log::error('PayMongo Webhook: Error processing payment paid event', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'event' => $event
            ]);
            
            return response('Error processing payment', 500);
        }
    }

    /**
     * Handle failed payment webhook
     */
    private function handlePaymentFailed($event)
    {
        try {
            $paymentIntentId = $event['data']['attributes']['data']['id'] ?? null;
            
            if (!$paymentIntentId) {
                Log::warning('PayMongo Webhook: No payment intent ID in failed event');
                return response('No payment intent ID', 400);
            }

            $order = Order::where('paymongo_payment_intent_id', $paymentIntentId)->first();
            
            if (!$order) {
                Log::warning('PayMongo Webhook: Order not found for failed payment', [
                    'payment_intent_id' => $paymentIntentId
                ]);
                return response('Order not found', 404);
            }

            // Update order to failed status
            $order->update([
                'payment_status' => 'failed',
                'status' => 'cancelled'
            ]);

            Log::info('PayMongo Webhook: Order marked as failed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_intent_id' => $paymentIntentId
            ]);

            return response('Payment failure processed', 200);

        } catch (\Exception $e) {
            Log::error('PayMongo Webhook: Error processing payment failed event', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'event' => $event
            ]);
            
            return response('Error processing payment failure', 500);
        }
    }

    /**
     * Handle payment processing webhook
     */
    private function handlePaymentProcessing($event)
    {
        try {
            $paymentIntentId = $event['data']['attributes']['data']['id'] ?? null;
            
            if (!$paymentIntentId) {
                return response('No payment intent ID', 400);
            }

            $order = Order::where('paymongo_payment_intent_id', $paymentIntentId)->first();
            
            if ($order && $order->payment_status === 'pending') {
                Log::info('PayMongo Webhook: Payment processing for order', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'payment_intent_id' => $paymentIntentId
                ]);
            }

            return response('Processing acknowledged', 200);

        } catch (\Exception $e) {
            Log::error('PayMongo Webhook: Error processing payment processing event', [
                'message' => $e->getMessage(),
                'event' => $event
            ]);
            
            return response('Error processing', 500);
        }
    }

    /**
     * Handle payment method updated webhook
     */
    private function handlePaymentMethodUpdated($event)
    {
        try {
            Log::info('PayMongo Webhook: Payment method updated', [
                'event' => $event
            ]);

            return response('Payment method update acknowledged', 200);

        } catch (\Exception $e) {
            Log::error('PayMongo Webhook: Error processing payment method update', [
                'message' => $e->getMessage(),
                'event' => $event
            ]);
            
            return response('Error processing update', 500);
        }
    }

    /**
     * Verify webhook signature
     */
    private function verifyWebhookSignature($payload, $signature)
    {
        if (!$signature || !config('paymongo.webhook_secret')) {
            return false;
        }

        // PayMongo sends signature in format: "t=timestamp,v1=signature"
        // We need to extract the v1 signature
        $signatureParts = [];
        $elements = explode(',', $signature);
        
        foreach ($elements as $element) {
            $keyValue = explode('=', $element, 2);
            if (count($keyValue) === 2) {
                $signatureParts[$keyValue[0]] = $keyValue[1];
            }
        }

        if (!isset($signatureParts['v1'])) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, config('paymongo.webhook_secret'));
        
        return hash_equals($expectedSignature, $signatureParts['v1']);
    }

    /**
     * Test webhook endpoint (for development only)
     */
    public function testWebhook()
    {
        if (app()->environment('production')) {
            abort(404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Webhook endpoint is working',
            'timestamp' => now()->toISOString()
        ]);
    }
}