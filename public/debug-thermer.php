<?php
// debug-thermer.php - Simple diagnostic file
error_reporting(0);
ini_set('display_errors', 0);

// Clear any output
while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/json');

$order_id = $_GET['id'] ?? 0;

// Test 1: Can we output basic JSON?
if ($order_id == 999) {
    $a = array();
    $obj = new stdClass();
    $obj->type = 0;
    $obj->content = 'Basic Test Works';
    $obj->bold = 1;
    $obj->align = 1;
    $obj->format = 0;
    array_push($a, $obj);
    echo json_encode($a, JSON_FORCE_OBJECT);
    exit;
}

// Test 2: Can we load Laravel?
try {
    require_once __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();
    
    $laravel_loaded = true;
} catch (Exception $e) {
    $laravel_loaded = false;
    $laravel_error = $e->getMessage();
}

// Test 3: Can we access the database?
$order_found = false;
$items_count = 0;

if ($laravel_loaded && $order_id > 0) {
    try {
        $order = \App\Models\Order::with('orderItems.menuItem')->find($order_id);
        if ($order) {
            $order_found = true;
            $items_count = $order->orderItems->count();
        }
    } catch (Exception $e) {
        $order_found = false;
    }
}

// Output diagnostic results
$a = array();

$obj1 = new stdClass();
$obj1->type = 0;
$obj1->content = 'DIAGNOSTIC RESULTS';
$obj1->bold = 1;
$obj1->align = 1;
$obj1->format = 2;
array_push($a, $obj1);

$obj2 = new stdClass();
$obj2->type = 0;
$obj2->content = 'Order ID: ' . $order_id;
$obj2->bold = 0;
$obj2->align = 0;
$obj2->format = 0;
array_push($a, $obj2);

$obj3 = new stdClass();
$obj3->type = 0;
$obj3->content = 'Laravel: ' . ($laravel_loaded ? 'OK' : 'FAILED');
$obj3->bold = 0;
$obj3->align = 0;
$obj3->format = 0;
array_push($a, $obj3);

if (!$laravel_loaded) {
    $obj4 = new stdClass();
    $obj4->type = 0;
    $obj4->content = 'Error: ' . substr($laravel_error ?? 'Unknown', 0, 30);
    $obj4->bold = 0;
    $obj4->align = 0;
    $obj4->format = 0;
    array_push($a, $obj4);
}

$obj5 = new stdClass();
$obj5->type = 0;
$obj5->content = 'Order Found: ' . ($order_found ? 'YES' : 'NO');
$obj5->bold = 0;
$obj5->align = 0;
$obj5->format = 0;
array_push($a, $obj5);

$obj6 = new stdClass();
$obj6->type = 0;
$obj6->content = 'Items Count: ' . $items_count;
$obj6->bold = 0;
$obj6->align = 0;
$obj6->format = 0;
array_push($a, $obj6);

echo json_encode($a, JSON_FORCE_OBJECT);
?>