<?php
// Clear any output buffers
while (ob_get_level()) {
    ob_end_clean();
}

// Start clean output buffering  
ob_start();

// Set headers
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

try {
    // Get order ID
    $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($orderId <= 0) {
        throw new Exception('Invalid order ID');
    }

    // Include Laravel bootstrap to access database
    require_once __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    // Create a fake request to boot Laravel
    $request = Illuminate\Http\Request::create('/');
    $kernel->bootstrap();

    // Get order from database using Laravel's DB facade
    $order = \App\Models\Order::with('orderItems.menuItem')->find($orderId);

    if (!$order) {
        throw new Exception('Order not found');
    }

    // Calculate total from order items
    $calculatedTotal = 0;
    $orderItems = [];

    foreach ($order->orderItems as $item) {
        $quantity = (int)$item->quantity;
        $itemName = $item->name ?: ($item->menuItem ? $item->menuItem->name : 'Menu Item');

        // Calculate unit price
        $unitPrice = 0;
        if ($item->unit_price && $item->unit_price > 0) {
            $unitPrice = (float)$item->unit_price;
        } elseif ($item->total_price && $quantity > 0) {
            $unitPrice = (float)$item->total_price / $quantity;
        } elseif ($item->menuItem && $item->menuItem->price) {
            $unitPrice = (float)$item->menuItem->price;
        }

        $itemTotal = $unitPrice * $quantity;
        $calculatedTotal += $itemTotal;

        $orderItems[] = [
            'name' => $itemName,
            'quantity' => $quantity,
            'unitPrice' => $unitPrice,
            'itemTotal' => $itemTotal
        ];
    }

    // Use calculated total or fallback to order total
    $totalAmount = $calculatedTotal > 0 ? $calculatedTotal : (float)$order->total_amount;

    // Create array exactly like the documentation example
    $a = array();

    // Header
    $obj1 = new stdClass();
    $obj1->type = 0;
    $obj1->content = "L' PRIMERO CAFE";
    $obj1->bold = 2;
    $obj1->align = 1;
    $obj1->format = 2;
    array_push($a, $obj1);

    $obj2 = new stdClass();
    $obj2->type = 0;
    $obj2->content = 'SIP & SERVE APP';
    $obj2->bold = 1;
    $obj2->align = 1;
    $obj2->format = 0;
    array_push($a, $obj2);

    // Address
    $objAddr1 = new stdClass();
    $objAddr1->type = 0;
    $objAddr1->content = 'Diversion Road, Sitio Sirangan, Macabog';
    $objAddr1->bold = 0;
    $objAddr1->align = 1;
    $objAddr1->format = 4;
    array_push($a, $objAddr1);

    $objAddr2 = new stdClass();
    $objAddr2->type = 0;
    $objAddr2->content = 'Sorsogon City, Sorsogon';
    $objAddr2->bold = 0;
    $objAddr2->align = 1;
    $objAddr2->format = 4;
    array_push($a, $objAddr2);

    $obj2b = new stdClass();
    $obj2b->type = 0;
    $obj2b->content = 'Official Receipt';
    $obj2b->bold = 1;
    $obj2b->align = 1;
    $obj2b->format = 0;
    array_push($a, $obj2b);

    $obj3 = new stdClass();
    $obj3->type = 0;
    $obj3->content = '================================';
    $obj3->bold = 0;
    $obj3->align = 1;
    $obj3->format = 0;
    array_push($a, $obj3);

    // Order details
    $obj4 = new stdClass();
    $obj4->type = 0;
    $obj4->content = 'Receipt: ' . ($order->order_number ?: str_pad($order->id, 4, '0', STR_PAD_LEFT));
    $obj4->bold = 0;
    $obj4->align = 0;
    $obj4->format = 0;
    array_push($a, $obj4);

    $obj5 = new stdClass();
    $obj5->type = 0;
    $obj5->content = 'Date: ' . date('M d, Y H:i', strtotime($order->created_at));
    $obj5->bold = 0;
    $obj5->align = 0;
    $obj5->format = 0;
    array_push($a, $obj5);

    $obj6 = new stdClass();
    $obj6->type = 0;
    $obj6->content = 'Type: ' . ucfirst($order->order_type ?: 'Dine-in');
    $obj6->bold = 1;
    $obj6->align = 0;
    $obj6->format = 0;
    array_push($a, $obj6);

    // Customer name if available
    if ($order->customer_name) {
        $objCustomer = new stdClass();
        $objCustomer->type = 0;
        $objCustomer->content = 'Customer: ' . substr($order->customer_name, 0, 20);
        $objCustomer->bold = 0;
        $objCustomer->align = 0;
        $objCustomer->format = 0;
        array_push($a, $objCustomer);
    }

    $obj7 = new stdClass();
    $obj7->type = 0;
    $obj7->content = '--------------------------------';
    $obj7->bold = 0;
    $obj7->align = 0;
    $obj7->format = 0;
    array_push($a, $obj7);

    // Order/s header
    $objOrderHeader = new stdClass();
    $objOrderHeader->type = 0;
    $objOrderHeader->content = 'Order/s:';
    $objOrderHeader->bold = 1;
    $objOrderHeader->align = 0;
    $objOrderHeader->format = 0;
    array_push($a, $objOrderHeader);

    // Add real order items
    foreach ($orderItems as $item) {
        // Item name with quantity
        $objItemName = new stdClass();
        $objItemName->type = 0;
        $objItemName->content = substr($item['name'], 0, 25);
        $objItemName->bold = 0;
        $objItemName->align = 0;
        $objItemName->format = 0;
        array_push($a, $objItemName);

        // Item calculation
        $objItemCalc = new stdClass();
        $objItemCalc->type = 0;
        $objItemCalc->content = '  ' . $item['quantity'] . ' x P' . number_format($item['unitPrice'], 2) . ' = P' . number_format($item['itemTotal'], 2);
        $objItemCalc->bold = 0;
        $objItemCalc->align = 2;
        $objItemCalc->format = 0;
        array_push($a, $objItemCalc);
    }

    $obj10 = new stdClass();
    $obj10->type = 0;
    $obj10->content = '--------------------------------';
    $obj10->bold = 0;
    $obj10->align = 0;
    $obj10->format = 0;
    array_push($a, $obj10);

    // DISCOUNT SECTION - Only if discount applied
    if ($hasDiscount) {
        // Subtotal before discount
        $objSubtotal = new stdClass();
        $objSubtotal->type = 0;
        $objSubtotal->content = 'Subtotal: P' . number_format($amountBeforeDiscount, 2);
        $objSubtotal->bold = 0;
        $objSubtotal->align = 2;
        $objSubtotal->format = 0;
        array_push($a, $objSubtotal);

        // Discount type label
        $discountLabel = $order->discount_type === 'senior_citizen' ? 'Senior Citizen' : 'PWD';
        $objDiscountType = new stdClass();
        $objDiscountType->type = 0;
        $objDiscountType->content = $discountLabel . ' Discount (20%)';
        $objDiscountType->bold = 1;
        $objDiscountType->align = 0;
        $objDiscountType->format = 0;
        array_push($a, $objDiscountType);

        // Discount ID number
        if ($order->discount_id_number) {
            $objDiscountID = new stdClass();
            $objDiscountID->type = 0;
            $objDiscountID->content = '  ID: ' . $order->discount_id_number;
            $objDiscountID->bold = 0;
            $objDiscountID->align = 0;
            $objDiscountID->format = 4;
            array_push($a, $objDiscountID);
        }

        // Discount amount (negative)
        $objDiscountAmt = new stdClass();
        $objDiscountAmt->type = 0;
        $objDiscountAmt->content = 'Discount: -P' . number_format($discountAmount, 2);
        $objDiscountAmt->bold = 0;
        $objDiscountAmt->align = 2;
        $objDiscountAmt->format = 0;
        array_push($a, $objDiscountAmt);

        // Separator before total
        $objSep = new stdClass();
        $objSep->type = 0;
        $objSep->content = '--------------------------------';
        $objSep->bold = 0;
        $objSep->align = 0;
        $objSep->format = 0;
        array_push($a, $objSep);
    }

    // ========== END OF DISCOUNT SECTION ==========

    // Total (after discount if applicable) - THIS IS THE EXISTING $obj11
    $obj11 = new stdClass();
    $obj11->type = 0;
    $obj11->content = 'TOTAL: P' . number_format($finalTotal, 2);  // Use $finalTotal instead of $totalAmount
    $obj11->bold = 1;
    $obj11->align = 2;
    $obj11->format = 1;
    array_push($a, $obj11);

    // Payment details
    if ($order->cash_amount) {
        $obj12 = new stdClass();
        $obj12->type = 0;
        $obj12->content = 'Cash: P' . number_format((float)$order->cash_amount, 2);
        $obj12->bold = 0;
        $obj12->align = 0;
        $obj12->format = 0;
        array_push($a, $obj12);

        if ($order->change_amount > 0) {
            $objChange = new stdClass();
            $objChange->type = 0;
            $objChange->content = 'Change: P' . number_format((float)$order->change_amount, 2);
            $objChange->bold = 0;
            $objChange->align = 0;
            $objChange->format = 0;
            array_push($a, $objChange);
        }
    }

    // Payment method
    if ($order->payment_method) {
        $objPayment = new stdClass();
        $objPayment->type = 0;
        $objPayment->content = 'Payment: ' . strtoupper($order->payment_method);
        $objPayment->bold = 0;
        $objPayment->align = 0;
        $objPayment->format = 0;
        array_push($a, $objPayment);
    }

    $obj13 = new stdClass();
    $obj13->type = 0;
    $obj13->content = '================================';
    $obj13->bold = 0;
    $obj13->align = 1;
    $obj13->format = 0;
    array_push($a, $obj13);

    $obj14 = new stdClass();
    $obj14->type = 0;
    $obj14->content = 'Thank you for dining with us!';
    $obj14->bold = 1;
    $obj14->align = 1;
    $obj14->format = 0;
    array_push($a, $obj14);

    // Business details
    $objSeparator = new stdClass();
    $objSeparator->type = 0;
    $objSeparator->content = '--------------------------------';
    $objSeparator->bold = 0;
    $objSeparator->align = 1;
    $objSeparator->format = 0;
    array_push($a, $objSeparator);

    $objPhone = new stdClass();
    $objPhone->type = 0;
    $objPhone->content = 'Tel: 0993-688-1248';
    $objPhone->bold = 0;
    $objPhone->align = 1;
    $objPhone->format = 4;
    array_push($a, $objPhone);

    $objBIR = new stdClass();
    $objBIR->type = 0;
    $objBIR->content = 'BIR #: 2819550';
    $objBIR->bold = 0;
    $objBIR->align = 1;
    $objBIR->format = 4;
    array_push($a, $objBIR);

    $objTIN = new stdClass();
    $objTIN->type = 0;
    $objTIN->content = 'TIN #: 269-004-339-000-00';
    $objTIN->bold = 0;
    $objTIN->align = 1;
    $objTIN->format = 4;
    array_push($a, $objTIN);

    // Add footer with timestamp
    $objFooter = new stdClass();
    $objFooter->type = 0;
    $objFooter->content = 'Printed: ' . date('Y-m-d H:i:s');
    $objFooter->bold = 0;
    $objFooter->align = 1;
    $objFooter->format = 4; // small text
    array_push($a, $objFooter);

    // Output exactly like the documentation
    ob_clean();
    echo json_encode($a, JSON_FORCE_OBJECT);
} catch (Exception $e) {
    // Error response
    $a = array();

    $obj1 = new stdClass();
    $obj1->type = 0;
    $obj1->content = 'Receipt Error';
    $obj1->bold = 1;
    $obj1->align = 1;
    $obj1->format = 0;
    array_push($a, $obj1);

    $obj2 = new stdClass();
    $obj2->type = 0;
    $obj2->content = 'Order #' . ($orderId ?: 'Unknown') . ' not found';
    $obj2->bold = 0;
    $obj2->align = 1;
    $obj2->format = 0;
    array_push($a, $obj2);

    $obj3 = new stdClass();
    $obj3->type = 0;
    $obj3->content = 'Please check order ID';
    $obj3->bold = 0;
    $obj3->align = 1;
    $obj3->format = 0;
    array_push($a, $obj3);

    ob_clean();
    echo json_encode($a, JSON_FORCE_OBJECT);
}

exit;
