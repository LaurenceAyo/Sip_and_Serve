<?php
// thermer-receipt.php - Clean working version
error_reporting(0);
ini_set('display_errors', 0);

// Clear any output
while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/json');

$order_id = $_GET['id'] ?? 0;

if (!$order_id || $order_id <= 0) {
    $a = array();
    $obj = new stdClass();
    $obj->type = 0;
    $obj->content = 'Invalid Order ID';
    $obj->bold = 1;
    $obj->align = 1;
    $obj->format = 0;
    array_push($a, $obj);
    echo json_encode($a, JSON_FORCE_OBJECT);
    exit;
}

try {
    require_once __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

    $order = \App\Models\Order::with('orderItems.menuItem')->find($order_id);

    if (!$order) {
        $a = array();
        $obj = new stdClass();
        $obj->type = 0;
        $obj->content = 'Order not found';
        $obj->bold = 1;
        $obj->align = 1;
        $obj->format = 0;
        array_push($a, $obj);
        echo json_encode($a, JSON_FORCE_OBJECT);
        exit;
    }

    $a = array();

    // Store header
    $obj1 = new stdClass();
    $obj1->type = 0;
    $obj1->content = 'L PRIMERO CAFE';
    $obj1->bold = 1;
    $obj1->align = 1;
    $obj1->format = 2;
    array_push($a, $obj1);

    // Address
    $obj2 = new stdClass();
    $obj2->type = 0;
    $obj2->content = 'Diversion Road, Sitio Sirangan';
    $obj2->bold = 0;
    $obj2->align = 1;
    $obj2->format = 0;
    array_push($a, $obj2);

    // Receipt number
    $obj3 = new stdClass();
    $obj3->type = 0;
    $obj3->content = 'Receipt: ' . ($order->order_number ?? str_pad($order->id, 4, '0', STR_PAD_LEFT));
    $obj3->bold = 0;
    $obj3->align = 0;
    $obj3->format = 0;
    array_push($a, $obj3);

    // Date
    $obj4 = new stdClass();
    $obj4->type = 0;
    $obj4->content = 'Date: ' . $order->created_at->format('M d, Y H:i');
    $obj4->bold = 0;
    $obj4->align = 0;
    $obj4->format = 0;
    array_push($a, $obj4);

    // Order type
    $obj5 = new stdClass();
    $obj5->type = 0;
    $obj5->content = 'Type: ' . ucfirst($order->order_type ?? 'dine-in');
    $obj5->bold = 0;
    $obj5->align = 0;
    $obj5->format = 0;
    array_push($a, $obj5);

    // Separator
    $obj6 = new stdClass();
    $obj6->type = 0;
    $obj6->content = '--------------------------------';
    $obj6->bold = 0;
    $obj6->align = 0;
    $obj6->format = 0;
    array_push($a, $obj6);

    // Order items
    $subtotal = 0;
    if ($order->orderItems && $order->orderItems->count() > 0) {
        foreach ($order->orderItems as $item) {
            // Get item name
            $itemName = 'Unknown Item';
            if (!empty($item->name)) {
                $itemName = $item->name;
            } elseif ($item->menuItem && !empty($item->menuItem->name)) {
                $itemName = $item->menuItem->name;
            }
            
            $quantity = (int)($item->quantity ?? 1);
            $unitPrice = (float)($item->unit_price ?? 0);
            $itemTotal = $unitPrice * $quantity;
            $subtotal += $itemTotal;

            // Item name
            $itemObj1 = new stdClass();
            $itemObj1->type = 0;
            $itemObj1->content = substr($itemName, 0, 25);
            $itemObj1->bold = 0;
            $itemObj1->align = 0;
            $itemObj1->format = 0;
            array_push($a, $itemObj1);

            // Item details
            $itemObj2 = new stdClass();
            $itemObj2->type = 0;
            $itemObj2->content = '  ' . $quantity . ' x P' . number_format($unitPrice, 2) . ' = P' . number_format($itemTotal, 2);
            $itemObj2->bold = 0;
            $itemObj2->align = 2;
            $itemObj2->format = 0;
            array_push($a, $itemObj2);
        }
    } else {
        // No items found
        $noItemsObj = new stdClass();
        $noItemsObj->type = 0;
        $noItemsObj->content = 'No items found';
        $noItemsObj->bold = 0;
        $noItemsObj->align = 1;
        $noItemsObj->format = 0;
        array_push($a, $noItemsObj);
    }

    // Total separator
    $totalSep = new stdClass();
    $totalSep->type = 0;
    $totalSep->content = '--------------------------------';
    $totalSep->bold = 0;
    $totalSep->align = 0;
    $totalSep->format = 0;
    array_push($a, $totalSep);

    // Calculate final total
    $finalTotal = $subtotal > 0 ? $subtotal : (float)($order->total_amount ?? 0);

    // Subtotal
    $subtotalObj = new stdClass();
    $subtotalObj->type = 0;
    $subtotalObj->content = 'Subtotal: P' . number_format($finalTotal, 2);
    $subtotalObj->bold = 0;
    $subtotalObj->align = 2;
    $subtotalObj->format = 0;
    array_push($a, $subtotalObj);

    // Total
    $totalObj = new stdClass();
    $totalObj->type = 0;
    $totalObj->content = 'TOTAL: P' . number_format($finalTotal, 2);
    $totalObj->bold = 1;
    $totalObj->align = 2;
    $totalObj->format = 2;
    array_push($a, $totalObj);

    // Payment separator
    $paymentSep = new stdClass();
    $paymentSep->type = 0;
    $paymentSep->content = '--------------------------------';
    $paymentSep->bold = 0;
    $paymentSep->align = 0;
    $paymentSep->format = 0;
    array_push($a, $paymentSep);

    // Payment method
    $paymentMethod = strtoupper($order->payment_method ?? 'CASH');
    $paymentObj = new stdClass();
    $paymentObj->type = 0;
    $paymentObj->content = 'Payment Method: ' . $paymentMethod;
    $paymentObj->bold = 0;
    $paymentObj->align = 0;
    $paymentObj->format = 0;
    array_push($a, $paymentObj);

    // Payment details
    if ($paymentMethod === 'MAYA') {
        $mayaObj = new stdClass();
        $mayaObj->type = 0;
        $mayaObj->content = 'Maya Payment Completed';
        $mayaObj->bold = 1;
        $mayaObj->align = 0;
        $mayaObj->format = 0;
        array_push($a, $mayaObj);
    } elseif ($paymentMethod === 'CASH') {
        if (!empty($order->cash_amount) && $order->cash_amount > 0) {
            $cashObj = new stdClass();
            $cashObj->type = 0;
            $cashObj->content = 'Cash Received: P' . number_format((float)$order->cash_amount, 2);
            $cashObj->bold = 0;
            $cashObj->align = 0;
            $cashObj->format = 0;
            array_push($a, $cashObj);

            if (!empty($order->change_amount) && $order->change_amount > 0) {
                $changeObj = new stdClass();
                $changeObj->type = 0;
                $changeObj->content = 'Change: P' . number_format((float)$order->change_amount, 2);
                $changeObj->bold = 1;
                $changeObj->align = 0;
                $changeObj->format = 0;
                array_push($a, $changeObj);
            }
        }
    }

    // Footer
    $footerSep = new stdClass();
    $footerSep->type = 0;
    $footerSep->content = '================================';
    $footerSep->bold = 0;
    $footerSep->align = 1;
    $footerSep->format = 0;
    array_push($a, $footerSep);

    $thankYou = new stdClass();
    $thankYou->type = 0;
    $thankYou->content = 'Thank you for dining with us!';
    $thankYou->bold = 0;
    $thankYou->align = 1;
    $thankYou->format = 0;
    array_push($a, $thankYou);

    $contact = new stdClass();
    $contact->type = 0;
    $contact->content = 'Phone: 0993-688-1248';
    $contact->bold = 0;
    $contact->align = 1;
    $contact->format = 0;
    array_push($a, $contact);

    echo json_encode($a, JSON_FORCE_OBJECT);

} catch (Exception $e) {
    $a = array();
    $errorObj = new stdClass();
    $errorObj->type = 0;
    $errorObj->content = 'System Error';
    $errorObj->bold = 1;
    $errorObj->align = 1;
    $errorObj->format = 0;
    array_push($a, $errorObj);
    echo json_encode($a, JSON_FORCE_OBJECT);
}
?>