<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function processCashPayment(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $orderData = $request->all();
            
            // Create order
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'order_type' => $orderData['orderType'] ?? 'takeout',
                'table_number' => $orderData['tableNumber'] ?? null,
                'total_amount' => $orderData['exactPaymentAmount'],
                'payment_method' => 'cash',
                'cash_amount' => $orderData['cashAmount'] ?? $orderData['exactPaymentAmount'],
                'change_amount' => max(0, ($orderData['cashAmount'] ?? $orderData['exactPaymentAmount']) - $orderData['exactPaymentAmount']),
                'status' => 'pending',
                'estimated_prep_time' => 15 // minutes
            ]);

            // Create order items
            foreach ($orderData['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['menu_item_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unitPrice'],
                    'total_price' => $item['unitPrice'] * $item['quantity'],
                    'special_instructions' => is_array($item['modifiers']) ? implode(', ', $item['modifiers']) : null,
                    'status' => 'pending'
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'order_id' => $order->id,
                'order_number' => $order->order_number
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Payment processing failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateOrderNumber()
    {
        $lastOrder = Order::latest()->first();
        return $lastOrder ? $lastOrder->id + 1 : 1001;
    }
}