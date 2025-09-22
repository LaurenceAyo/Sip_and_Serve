<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;

class PrinterJsonController extends Controller
{
    public function receipt($id)
    {
        try {
            $order = Order::with('orderItems.menuItem')->findOrFail($id);

            $lines = [];

            // Header
            $lines[] = [
                'type' => 0,
                'content' => 'SIP & SERVE CAFE',
                'bold' => 1,
                'align' => 1,
                'format' => 0
            ];

            $lines[] = [
                'type' => 0,
                'content' => '================================',
                'bold' => 0,
                'align' => 1,
                'format' => 0
            ];

            // Order details
            $lines[] = [
                'type' => 0,
                'content' => 'Order: ' . ($order->order_number ?? $order->id),
                'bold' => 1,
                'align' => 0,
                'format' => 0
            ];

            $lines[] = [
                'type' => 0,
                'content' => 'Date: ' . $order->created_at->format('Y-m-d H:i'),
                'bold' => 0,
                'align' => 0,
                'format' => 0
            ];

            $lines[] = [
                'type' => 0,
                'content' => '--------------------------------',
                'bold' => 0,
                'align' => 0,
                'format' => 0
            ];

            // Items
            foreach ($order->orderItems as $item) {
                $lines[] = [
                    'type' => 0,
                    'content' => $item->name,
                    'bold' => 0,
                    'align' => 0,
                    'format' => 0
                ];

                $lines[] = [
                    'type' => 0,
                    'content' => '  ' . $item->quantity . ' x P' . number_format($item->unit_price, 2),
                    'bold' => 0,
                    'align' => 0,
                    'format' => 0
                ];
            }

            $lines[] = [
                'type' => 0,
                'content' => '--------------------------------',
                'bold' => 0,
                'align' => 0,
                'format' => 0
            ];

            $lines[] = [
                'type' => 0,
                'content' => 'TOTAL: P' . number_format((float) $order->total_amount, 2),
                'bold' => 1,
                'align' => 1,
                'format' => 0
            ];

            $lines[] = [
                'type' => 0,
                'content' => 'Thank you!',
                'bold' => 1,
                'align' => 1,
                'format' => 0
            ];

            // Return as simple indexed array
            return response()->json($lines);
        } catch (\Exception $e) {
            return response()->json([[
                'type' => 0,
                'content' => 'Error: Order not found',
                'bold' => 1,
                'align' => 1,
                'format' => 0
            ]]);
        }
    }
}
