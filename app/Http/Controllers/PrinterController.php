<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order; // adjust if your model is named differently

class PrinterController extends Controller
{
    /**
     * JSON response for Thermer (Bluetooth Print App).
     * Thermer will fetch this endpoint and print the JSON.
     */
    public function response()
    {
        // Return a test response or redirect to a specific order
        return response()->json([
            [
                'type' => 0,
                'content' => 'Thermer Connection Test',
                'bold' => 1,
                'align' => 1,
                'format' => 0
            ]
        ]);
    }
    public function receipt($id)
    {
        $order = Order::with('orderItems.menuItem')->findOrFail($id);

        $a = [];

        // === HEADER ===
        $obj1 = new \stdClass();
        $obj1->type = 0; // text
        $obj1->content = 'SIP & SERVE CAFE';
        $obj1->bold = 1;
        $obj1->align = 1; // center
        $obj1->format = 2; // double height + width
        $a[] = $obj1;

        $obj2 = new \stdClass();
        $obj2->type = 0;
        $obj2->content = 'Official Receipt';
        $obj2->bold = 1;
        $obj2->align = 1;
        $obj2->format = 1; // double height
        $a[] = $obj2;

        $obj3 = new \stdClass();
        $obj3->type = 0;
        $obj3->content = '========================';
        $obj3->bold = 0;
        $obj3->align = 1;
        $a[] = $obj3;

        // === ORDER INFO ===
        $a[] = (object)[
            "type" => 0,
            "content" => "Receipt: " . ($order->order_number ?? str_pad($order->id, 4, '0', STR_PAD_LEFT)),
            "bold" => 0,
            "align" => 0
        ];
        $a[] = (object)[
            "type" => 0,
            "content" => "Date: " . $order->created_at->format('M d, Y H:i'),
            "bold" => 0,
            "align" => 0
        ];
        $a[] = (object)[
            "type" => 0,
            "content" => "Type: " . ucfirst($order->order_type ?? 'Dine-in'),
            "bold" => 0,
            "align" => 0
        ];
        if ($order->customer_name) {
            $a[] = (object)[
                "type" => 0,
                "content" => "Customer: " . substr($order->customer_name, 0, 20),
                "bold" => 0,
                "align" => 0
            ];
        }

        $a[] = (object)[
            "type" => 0,
            "content" => "------------------------",
            "bold" => 0,
            "align" => 0
        ];

        // === ITEMS ===
        foreach ($order->orderItems as $item) {
            $itemName = $item->name ?? $item->menuItem->name ?? 'Custom Item';
            $quantity = (int) $item->quantity;
            $unitPrice = $item->unit_price ?? ($item->total_price / max(1, $quantity));
            $totalPrice = $unitPrice * $quantity;

            $a[] = (object)[
                "type" => 0,
                "content" => substr($itemName, 0, 20),
                "bold" => 0,
                "align" => 0
            ];
            $a[] = (object)[
                "type" => 0,
                "content" => "  {$quantity} x " . number_format($unitPrice, 2) . " = " . number_format($totalPrice, 2),
                "bold" => 0,
                "align" => 2
            ];
        }

        $a[] = (object)[
            "type" => 0,
            "content" => "------------------------",
            "bold" => 0,
            "align" => 0
        ];

        // === TOTALS ===
        $subtotal = $order->orderItems->sum(fn($i) => ($i->unit_price ?? ($i->total_price / max(1, $i->quantity))) * $i->quantity);
        $tax = $order->tax_amount ?? 0;
        $discount = $order->discount_amount ?? 0;
        $grandTotal = $subtotal + $tax - $discount;

        $a[] = (object)[
            "type" => 0,
            "content" => "Subtotal: " . number_format($subtotal, 2),
            "bold" => 0,
            "align" => 2
        ];
        if ($tax > 0) {
            $a[] = (object)[
                "type" => 0,
                "content" => "VAT 12%: " . number_format($tax, 2),
                "bold" => 0,
                "align" => 2
            ];
        }
        if ($discount > 0) {
            $a[] = (object)[
                "type" => 0,
                "content" => "Discount: -" . number_format($discount, 2),
                "bold" => 0,
                "align" => 2
            ];
        }

        $a[] = (object)[
            "type" => 0,
            "content" => "TOTAL: " . number_format($grandTotal, 2),
            "bold" => 1,
            "align" => 2,
            "format" => 2
        ];

        // === PAYMENT DETAILS ===
        $a[] = (object)[
            "type" => 0,
            "content" => "Payment: CASH",
            "bold" => 0,
            "align" => 0
        ];
        $a[] = (object)[
            "type" => 0,
            "content" => "Cash: " . number_format($order->cash_amount ?? 0, 2),
            "bold" => 0,
            "align" => 0
        ];
        if ($order->change_amount > 0) {
            $a[] = (object)[
                "type" => 0,
                "content" => "Change: " . number_format((float) $order->change_amount, 2),
                "bold" => 0,
                "align" => 0
            ];
        }

        $a[] = (object)[
            "type" => 0,
            "content" => "========================",
            "bold" => 0,
            "align" => 1
        ];

        // === FOOTER ===
        $a[] = (object)[
            "type" => 0,
            "content" => "Thank you for dining with us!",
            "bold" => 0,
            "align" => 1
        ];
        $a[] = (object)[
            "type" => 0,
            "content" => "BIR: 2819550",
            "bold" => 0,
            "align" => 1
        ];
        $a[] = (object)[
            "type" => 0,
            "content" => "TIN: 269-004-339-000-00",
            "bold" => 0,
            "align" => 1
        ];
        $a[] = (object)[
            "type" => 0,
            "content" => "www.sipandserve.com",
            "bold" => 0,
            "align" => 1
        ];

        return response()->json($a);
    }
}
