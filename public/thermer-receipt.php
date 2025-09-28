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
    $obj1->content = 'SIP & SERVE CAFE';
    $obj1->bold = 1;
    $obj1->align = 1;
    $obj1->format = 2;
    array_push($a, $obj1);
    
    $obj2 = new stdClass();
    $obj2->type = 0;
    $obj2->content = 'Official Receipt';
    $obj2->bold = 1;
    $obj2->align = 1;
    $obj2->format = 0;
    array_push($a, $obj2);
    
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
    $obj6->bold = 0;
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
    
    // Total
    $obj11 = new stdClass();
    $obj11->type = 0;
    $obj11->content = 'TOTAL: P' . number_format($totalAmount, 2);
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
    $obj14->bold = 0;
    $obj14->align = 1;
    $obj14->format = 0;
    array_push($a, $obj14);
    
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
?>