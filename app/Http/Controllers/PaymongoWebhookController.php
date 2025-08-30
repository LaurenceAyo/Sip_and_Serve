<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class PayMongoWebhookController extends Controller
{
    /**
     * Handle failed payment
     */
    private function handlePaymentFailure($paymentData)
    {
        $paymentIntentId = $paymentData['id'] ?? null;
        
        Log::warning('Payment failed', [
            'payment_intent_id' => $paymentIntentId,
            'payment_data' => $paymentData
        ]);

        // Optionally update order status to failed
        $order = Order::where('payment_intent_id', $paymentIntentId)->first();
        if ($order) {
            $order->update([
                'payment_status' => 'failed',
                'payment_data' => json_encode($paymentData)
            ]);
        }

        return response()->json(['status' => 'payment_failure_logged'], 200);
    }

    /**
     * Handle PayMongo webhook events
     */
    public function handleWebhook(Request $request)
    {
        try {
            $eventType = $request->input('data.attributes.type');
            $eventData = $request->input('data.attributes.data');
            
            Log::info('PayMongo webhook received', [
                'event_type' => $eventType,
                'event_data' => $eventData
            ]);

            // Handle payment success events
            if (in_array($eventType, ['payment_intent.succeeded', 'payment.paid', 'checkout_session.payment.paid'])) {
                return $this->handlePaymentSuccess($eventData);
            }

            // Handle payment failure
            if (in_array($eventType, ['payment.failed', 'payment_intent.failed'])) {
                return $this->handlePaymentFailure($eventData);
            }

            // Handle other events as needed
            return response()->json(['status' => 'ignored'], 200);
            
        } catch (\Exception $e) {
            Log::error('PayMongo webhook error', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Handle successful payment
     */
    private function handlePaymentSuccess($paymentData)
    {
        $paymentIntentId = $paymentData['id'] ?? null;
        
        if (!$paymentIntentId) {
            Log::error('No payment intent ID in webhook data');
            return response()->json(['error' => 'Invalid payment data'], 400);
        }

        Log::info('Processing payment success', ['payment_intent_id' => $paymentIntentId]);

        // Method 1: Try to find order by payment_intent_id column
        $order = Order::where('payment_intent_id', $paymentIntentId)->first();
        
        if ($order) {
            Log::info('Order found by payment_intent_id', ['order_id' => $order->id]);
            return $this->processOrderPayment($order, $paymentData);
        }

        // Method 2: Try to find order from PayMongo payment intent metadata
        $order = $this->findOrderFromPaymentIntentMetadata($paymentIntentId);
        
        if ($order) {
            Log::info('Order found from metadata', ['order_id' => $order->id]);
            // Update the order with payment intent ID for future reference
            $order->update(['payment_intent_id' => $paymentIntentId]);
            return $this->processOrderPayment($order, $paymentData);
        }

        // Method 3: Try to match by amount and recent timestamp (last resort)
        $paymentAmount = $paymentData['amount'] ?? 0;
        $order = $this->findOrderByAmountAndTime($paymentAmount);
        
        if ($order) {
            Log::info('Order found by amount matching', ['order_id' => $order->id]);
            $order->update(['payment_intent_id' => $paymentIntentId]);
            return $this->processOrderPayment($order, $paymentData);
        }

        Log::error('Payment success failed - no order found', [
            'payment_intent_id' => $paymentIntentId,
            'amount' => $paymentAmount
        ]);
        
        return response()->json(['error' => 'Order not found'], 404);
    }

    /**
     * Find order from PayMongo payment intent metadata
     */
    private function findOrderFromPaymentIntentMetadata($paymentIntentId)
    {
        try {
            // Fetch payment intent from PayMongo API
            $paymentIntent = $this->getPaymentIntentFromPayMongo($paymentIntentId);
            
            if (!$paymentIntent) {
                return null;
            }

            $metadata = $paymentIntent['attributes']['metadata'] ?? [];
            
            // Try to find by order ID in metadata
            if (isset($metadata['order_id'])) {
                $order = Order::find($metadata['order_id']);
                if ($order) return $order;
            }
            
            // Try to find by order number in metadata  
            if (isset($metadata['order_number'])) {
                $order = Order::where('order_number', $metadata['order_number'])->first();
                if ($order) return $order;
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Error fetching payment intent metadata', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Find order by matching amount and recent creation time
     */
    private function findOrderByAmountAndTime($paymentAmount)
    {
        // Convert cents to peso (PayMongo uses cents)
        $amountInPeso = $paymentAmount / 100;
        
        // Look for orders with matching total amount created in the last 30 minutes
        return Order::where('total_amount', $amountInPeso)
            ->where('payment_intent_id', null) // Not yet processed
            ->where('created_at', '>=', now()->subMinutes(30))
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Process the order payment
     */
    private function processOrderPayment($order, $paymentData)
    {
        try {
            // Update order status
            $order->update([
                'payment_method' => 'gcash', // or extract from payment data
                'payment_status' => 'paid',
                'paid_at' => now(),
                'payment_data' => json_encode($paymentData) // Store full payment data
            ]);

            Log::info('Order payment processed successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number
            ]);

            // Add any additional order processing logic here
            // e.g., send confirmation email, update inventory, etc.

            return response()->json(['status' => 'success', 'order_id' => $order->id], 200);
            
        } catch (\Exception $e) {
            Log::error('Error processing order payment', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Payment processing failed'], 500);
        }
    }

    /**
     * Fetch payment intent from PayMongo API
     */
    private function getPaymentIntentFromPayMongo($paymentIntentId)
    {
        try {
            $secretKey = config('paymongo.secret_key');
            
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($secretKey . ':'),
                'Content-Type' => 'application/json',
            ])->get("https://api.paymongo.com/v1/payment_intents/{$paymentIntentId}");

            if ($response->successful()) {
                return $response->json()['data'];
            }
            
            Log::error('Failed to fetch payment intent from PayMongo', [
                'payment_intent_id' => $paymentIntentId,
                'status' => $response->status()
            ]);
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Error calling PayMongo API', [
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }
}