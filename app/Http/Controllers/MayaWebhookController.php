<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Events\PaymentReceived;

class MayaWebhookController extends Controller
{
    /**
     * Handle Maya webhook notifications
     * This will automatically update orders when payments come through
     */
    public function handleWebhook(Request $request)
    {
        Log::info('Maya Webhook Received', [
            'headers' => $request->headers->all(),
            'payload' => $request->all()
        ]);

        try {
            $payload = $request->all();

            $referenceNumber = $payload['reference_number'] ??
                $payload['ref_no'] ??
                $payload['transactionId'] ??
                $payload['referenceId'] ?? null;

            $amount = $payload['amount'] ?? $payload['totalAmount'] ?? null;
            $status = $payload['status'] ?? null;

            // Log ALL webhook attempts
            DB::table('maya_payment_logs')->insert([
                'reference_number' => $referenceNumber ?? 'UNKNOWN',
                'amount' => $amount,
                'status' => $status,
                'payload' => json_encode($payload),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            if (!$referenceNumber) {
                Log::warning('Maya webhook missing reference number', ['payload' => $payload]);
                return response()->json(['success' => false, 'message' => 'Missing reference number'], 400);
            }

            // Find PREPARING Maya orders that haven't been confirmed yet
            $matchedOrder = Order::where('payment_method', 'maya')
                ->where('payment_status', 'paid')
                ->where('status', 'preparing')
                ->whereNull('kitchen_received_at') // Not confirmed by cashier yet
                ->where(function ($query) use ($amount) {
                    if ($amount) {
                        $query->whereBetween('total_amount', [
                            floatval($amount) - 1,
                            floatval($amount) + 1
                        ]);
                    }
                })
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$matchedOrder) {
                Log::warning('No matching Maya order found', [
                    'reference_number' => $referenceNumber,
                    'amount' => $amount
                ]);
                return response()->json(['success' => false, 'message' => 'No matching order'], 404);
            }

            // Update order with Maya reference (but DON'T confirm yet - cashier needs to verify)
            $matchedOrder->update([
                'maya_reference' => $referenceNumber,
                'maya_payment_data' => json_encode($payload),
                'maya_webhook_received_at' => now(),
                // DON'T set kitchen_received_at - cashier needs to confirm first
            ]);

            Log::info('Maya reference attached to order', [
                'order_id' => $matchedOrder->id,
                'order_number' => $matchedOrder->order_number,
                'reference_number' => $referenceNumber,
                'amount' => $amount
            ]);

            // Broadcast real-time update to cashier UI
            try {
                broadcast(new \App\Events\MayaReferenceReceived($matchedOrder))->toOthers();
            } catch (\Exception $e) {
                Log::warning('Broadcast failed but reference saved', ['error' => $e->getMessage()]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Reference number attached to order',
                'order_id' => $matchedOrder->id,
                'order_number' => $matchedOrder->order_number,
                'reference_number' => $referenceNumber
            ], 200);
        } catch (\Exception $e) {
            Log::error('Maya webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Webhook processing failed'
            ], 500);
        }
    }

    /**
     * Quick confirm - cashier just clicks "confirm" after visual verification
     */
    public function quickConfirm(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id'
        ]);

        try {
            $order = Order::findOrFail($validated['order_id']);

            // Verify order has Maya reference
            if (!$order->maya_reference) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Maya reference found. Please wait for payment notification.'
                ], 400);
            }

            // Mark as paid
            $order->update([
                'payment_status' => 'paid',
                'status' => 'preparing', // Keep as preparing for kitchen
                'paid_at' => now(),
                'kitchen_received_at' => now(), // Mark as confirmed
                'confirmed_by' => Auth::id() ?? null // ← FIX: Make it nullable
            ]);

            Log::info('Maya payment quick-confirmed', [
                'order_id' => $order->id,
                'reference_number' => $order->maya_reference,
                'confirmed_by' => Auth::id()
            ]);

            // Broadcast
            broadcast(new PaymentReceived($order))->toOthers();

            return response()->json([
                'success' => true,
                'message' => 'Payment confirmed',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'maya_reference' => $order->maya_reference
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Quick confirm failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Confirmation failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
