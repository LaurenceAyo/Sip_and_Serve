<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\OrderReceiptMail;
use Exception;

class OrderController extends Controller
{
    public function kitchen()
    {
        $pendingOrders = Order::with(['orderItems.menuItem'])
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get();

        $processingOrders = Order::with(['orderItems.menuItem'])
            ->where('status', 'processing')
            ->orderBy('updated_at')
            ->get();

        return view('kitchen', compact('pendingOrders', 'processingOrders'));
    }

    public function startOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->update([
            'status' => 'processing',
            'started_at' => now()
        ]);

        return redirect()->route('kitchen.index')->with('success', 'Order started!');
    }

    public function completeOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        return redirect()->route('kitchen.index')->with('success', 'Order completed!');
    }

    public function sendReceipt(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'order_id' => 'required',
        ]);

        try {
            // Get order details
            $order = Order::find($validated['order_id']);
            
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            // Send email using Laravel Mail
            Mail::to($validated['email'])->send(new OrderReceiptMail($order));
            
            return response()->json([
                'success' => true,
                'message' => 'Receipt sent successfully'
            ]);
            
        } catch (Exception $e) {
            // Log the error for debugging
            Log::error('Failed to send receipt email', [
                'error' => $e->getMessage(),
                'order_id' => $validated['order_id'],
                'email' => $validated['email']
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send receipt'
            ], 500);
        }
    }
}