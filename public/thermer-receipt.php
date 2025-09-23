<?php
// thermer-receipt.php - Clean JSON output for Thermer
header('Content-Type: application/json');
header('Cache-Control: no-cache');

// Get order ID from URL parameter
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($order_id <= 0) {
    $response = [
        [
            'type' => 0,
            'content' => 'Invalid Order ID',
            'bold' => 1,
            'align' => 1,
            'format' => 0
        ]
    ];
    echo json_encode($response, JSON_FORCE_OBJECT);
    exit;
}

try {
    // Include Laravel's database connection
    require_once __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

    // Get order data
    $order = \App\Models\Order::with('orderItems.menuItem')->find($order_id);

    if (!$order) {
        $response = [
            [
                'type' => 0,
                'content' => 'Order not found',
                'bold' => 1,
                'align' => 1,
                'format' => 0
            ]
        ];
        echo json_encode($response, JSON_FORCE_OBJECT);
        exit;
    }

    $a = array();

    // Header
    $obj1 = new stdClass();
    $obj1->type = 0;
    $obj1->content = 'L PRIMERO CAFE';
    $obj1->bold = 1;
    $obj1->align = 1;
    $obj1->format = 2;
    array_push($a, $obj1);

    $obj2 = new stdClass();
    $obj2->type = 0;
    $obj2->content = 'Diversion Road, Sitio Sirangan   Macabog Sorsogon City, Sorsogon';
    $obj2->bold = 1;
    $obj2->align = 1;
    $obj2->format = 0;
    array_push($a, $obj2);

    $obj3 = new stdClass();
    $obj3->type = 0;
    $obj3->content = 'SIP & SERVE App';
    $obj3->bold = 1;
    $obj3->align = 1;
    $obj3->format = 0;
    array_push($a, $obj3);

    $obj4 = new stdClass();
    $obj4->type = 0;
    $obj4->content = 'Official Receipt';
    $obj4->bold = 1;
    $obj4->align = 1;
    $obj4->format = 0;
    array_push($a, $obj4);

    $obj5 = new stdClass();
    $obj5->type = 0;
    $obj5->content = '================================';
    $obj5->bold = 0;
    $obj5->align = 1;
    $obj5->format = 0;
    array_push($a, $obj5);

    // Order details
    $obj6 = new stdClass();
    $obj6->type = 0;
    $obj6->content = 'Receipt: ' . ($order->order_number ?? str_pad($order->id, 4, '0', STR_PAD_LEFT));
    $obj6->bold = 0;
    $obj6->align = 0;
    $obj6->format = 0;
    array_push($a, $obj6);

    $obj7 = new stdClass();
    $obj7->type = 0;
    $obj7->content = 'Date: ' . $order->created_at->format('M d, Y H:i');
    $obj7->bold = 0;
    $obj7->align = 0;
    $obj7->format = 0;
    array_push($a, $obj7);

    $obj8 = new stdClass();
    $obj8->type = 0;
    $obj8->content = 'Type: ' . ucfirst($order->order_type ?? 'Dine-in');
    $obj8->bold = 0;
    $obj8->align = 0;
    $obj8->format = 0;
    array_push($a, $obj8);

    // Customer name if available
    if ($order->customer_name) {
        $obj_customer = new stdClass();
        $obj_customer->type = 0;
        $obj_customer->content = 'Customer: ' . substr($order->customer_name, 0, 20);
        $obj_customer->bold = 0;
        $obj_customer->align = 0;
        $obj_customer->format = 0;
        array_push($a, $obj_customer);
    }

    // Items separator
    $obj_sep1 = new stdClass();
    $obj_sep1->type = 0;
    $obj_sep1->content = '--------------------------------';
    $obj_sep1->bold = 0;
    $obj_sep1->align = 0;
    $obj_sep1->format = 0;
    array_push($a, $obj_sep1);

    // Order items
    $total = 0;
    foreach ($order->orderItems as $item) {
        $itemName = $item->name ?? ($item->menuItem->name ?? 'Custom Item');
        $quantity = (int) $item->quantity;
        $unitPrice = $item->unit_price ?? ($item->total_price / max(1, $quantity));
        $itemTotal = $unitPrice * $quantity;
        $total += $itemTotal;

        // Item name
        $obj_item = new stdClass();
        $obj_item->type = 0;
        $obj_item->content = substr($itemName, 0, 25);
        $obj_item->bold = 0;
        $obj_item->align = 0;
        $obj_item->format = 0;
        array_push($a, $obj_item);

        // Item details
        $obj_details = new stdClass();
        $obj_details->type = 0;
        $obj_details->content = '  ' . $quantity . ' x P' . number_format($unitPrice, 2) . ' = P' . number_format($itemTotal, 2);
        $obj_details->bold = 0;
        $obj_details->align = 2;
        $obj_details->format = 0;
        array_push($a, $obj_details);
    }

    ////////////////////////////////////////////
    // Total separator
    // Total separator
    $obj_sep2 = new stdClass();
    $obj_sep2->type = 0;
    $obj_sep2->content = '--------------------------------';
    $obj_sep2->bold = 0;
    $obj_sep2->align = 0;
    $obj_sep2->format = 0;
    array_push($a, $obj_sep2);

    // === TOTALS ===
    $subtotal = $order->orderItems->sum(fn($i) => ($i->unit_price ?? ($i->total_price / max(1, $i->quantity))) * $i->quantity);
    $tax = $order->tax_amount ?? 0;
    $discount = $order->discount_amount ?? 0;
    $grandTotal = $subtotal + $tax - $discount;

    // Subtotal
    $obj_subtotal = new stdClass();
    $obj_subtotal->type = 0;
    $obj_subtotal->content = 'Subtotal: P' . number_format($subtotal, 2);
    $obj_subtotal->bold = 0;
    $obj_subtotal->align = 2;
    $obj_subtotal->format = 0;
    array_push($a, $obj_subtotal);

    // VAT (if applicable)
    if ($tax > 0) {
        $obj_vat = new stdClass();
        $obj_vat->type = 0;
        $obj_vat->content = 'VAT 12%: P' . number_format($tax, 2);
        $obj_vat->bold = 0;
        $obj_vat->align = 2;
        $obj_vat->format = 0;
        array_push($a, $obj_vat);
    }

    // Discount (if applicable)
    if ($discount > 0) {
        $obj_discount = new stdClass();
        $obj_discount->type = 0;
        $obj_discount->content = 'Discount: -P' . number_format($discount, 2);
        $obj_discount->bold = 0;
        $obj_discount->align = 2;
        $obj_discount->format = 0;
        array_push($a, $obj_discount);
    }

    // Grand Total
    $obj_total = new stdClass();
    $obj_total->type = 0;
    $obj_total->content = 'TOTAL: P' . number_format($grandTotal, 2);
    $obj_total->bold = 1;
    $obj_total->align = 2;
    $obj_total->format = 2;
    array_push($a, $obj_total);

    // Enhanced Payment details section
    $obj_payment_sep = new stdClass();
    $obj_payment_sep->type = 0;
    $obj_payment_sep->content = '--------------------------------';
    $obj_payment_sep->bold = 0;
    $obj_payment_sep->align = 0;
    $obj_payment_sep->format = 0;
    array_push($a, $obj_payment_sep);

    $obj_payment_header = new stdClass();
    $obj_payment_header->type = 0;
    $obj_payment_header->content = 'PAYMENT DETAILS';
    $obj_payment_header->bold = 1;
    $obj_payment_header->align = 1;
    $obj_payment_header->format = 0;
    array_push($a, $obj_payment_header);

    // Payment method
    $payment_method = strtoupper($order->payment_method ?? 'CASH');
    $obj_payment_method = new stdClass();
    $obj_payment_method->type = 0;
    $obj_payment_method->content = 'Payment Method: ' . $payment_method;
    $obj_payment_method->bold = 0;
    $obj_payment_method->align = 0;
    $obj_payment_method->format = 0;
    array_push($a, $obj_payment_method);

    // Cash received
    if ($order->cash_amount && $order->cash_amount > 0) {
        $obj_cash = new stdClass();
        $obj_cash->type = 0;
        $obj_cash->content = 'Cash Received: P' . number_format((float) $order->cash_amount, 2);
        $obj_cash->bold = 0;
        $obj_cash->align = 0;
        $obj_cash->format = 0;
        array_push($a, $obj_cash);

        // Change given
        if ($order->change_amount > 0) {
            $obj_change = new stdClass();
            $obj_change->type = 0;
            $obj_change->content = 'Change Given: P' . number_format((float) $order->change_amount, 2);
            $obj_change->bold = 1;
            $obj_change->align = 0;
            $obj_change->format = 0;
            array_push($a, $obj_change);
        } else {
            $obj_exact = new stdClass();
            $obj_exact->type = 0;
            $obj_exact->content = 'Exact Payment - No Change';
            $obj_exact->bold = 0;
            $obj_exact->align = 0;
            $obj_exact->format = 0;
            array_push($a, $obj_exact);
        }
    } else {
        $obj_no_payment = new stdClass();
        $obj_no_payment->type = 0;
        $obj_no_payment->content = 'Payment: Processing...';
        $obj_no_payment->bold = 0;
        $obj_no_payment->align = 0;
        $obj_no_payment->format = 0;
        array_push($a, $obj_no_payment);
    }

    // Payment status
    $payment_status = strtoupper($order->payment_status ?? 'PENDING');
    $obj_status = new stdClass();
    $obj_status->type = 0;
    $obj_status->content = 'Status: ' . $payment_status;
    $obj_status->bold = 1;
    $obj_status->align = 0;
    $obj_status->format = 0;
    array_push($a, $obj_status);

    // Payment timestamp if available
    if ($order->paid_at) {
        $obj_paid_at = new stdClass();
        $obj_paid_at->type = 0;
        $obj_paid_at->content = 'Paid At: ' . $order->paid_at->format('M d, Y H:i');
        $obj_paid_at->bold = 0;
        $obj_paid_at->align = 0;
        $obj_paid_at->format = 0;
        array_push($a, $obj_paid_at);
    }

    // Footer
    $obj_footer1 = new stdClass();
    $obj_footer1->type = 0;
    $obj_footer1->content = '================================';
    $obj_footer1->bold = 0;
    $obj_footer1->align = 1;
    $obj_footer1->format = 0;
    array_push($a, $obj_footer1);


    $obj_footer2 = new stdClass();
    $obj_footer2->type = 0;
    $obj_footer2->content = 'Thank you for dining with us!';
    $obj_footer2->bold = 0;
    $obj_footer2->align = 1;
    $obj_footer2->format = 0;
    array_push($a, $obj_footer2);

    $obj_footer4 = new stdClass();
    $obj_footer4->type = 0;
    $obj_footer4->content = '--------------------------------';
    $obj_footer4->bold = 0;
    $obj_footer4->align = 1;
    $obj_footer4->format = 0;
    array_push($a, $obj_footer4);

    $obj_footer5 = new stdClass();
    $obj_footer5->type = 0;
    $obj_footer5->content = 'Email: lprimerocoffee@gmail.com';
    $obj_footer5->bold = 0;
    $obj_footer5->align = 1;
    $obj_footer5->format = 0;
    array_push($a, $obj_footer5);

    $obj_footer6 = new stdClass();
    $obj_footer6->type = 0;
    $obj_footer6->content = 'Phone: 0993-688-1248';
    $obj_footer6->bold = 0;
    $obj_footer6->align = 1;
    $obj_footer6->format = 0;
    array_push($a, $obj_footer6);

    $obj_footer7 = new stdClass();
    $obj_footer7->type = 0;
    $obj_footer7->content = 'BIR: 2819550';
    $obj_footer7->bold = 0;
    $obj_footer7->align = 1;
    $obj_footer7->format = 0;
    array_push($a, $obj_footer7);

    $obj_footer8 = new stdClass();
    $obj_footer8->type = 0;
    $obj_footer8->content = 'TIN: 269-004-339-000-00';
    $obj_footer8->bold = 0;
    $obj_footer8->align = 1;
    $obj_footer8->format = 0;
    array_push($a, $obj_footer8);

    echo json_encode($a, JSON_FORCE_OBJECT);
} catch (Exception $e) {
    $response = [
        [
            'type' => 0,
            'content' => 'Error loading order data',
            'bold' => 1,
            'align' => 1,
            'format' => 0
        ]
    ];
    echo json_encode($response, JSON_FORCE_OBJECT);
}
