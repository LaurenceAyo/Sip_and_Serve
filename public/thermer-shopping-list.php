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
    // Load environment variables
    $envPath = __DIR__ . '/../.env';
    if (file_exists($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value, '"');
            }
        }
    }
    
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $database = $_ENV['DB_DATABASE'] ?? '';
    $username = $_ENV['DB_USERNAME'] ?? '';
    $password = $_ENV['DB_PASSWORD'] ?? '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Remove the current_stock > 0 filter to include zero-stock items
    $sql = "SELECT 
                i.id,
                i.menu_item_id,
                i.current_stock,
                i.minimum_stock,
                i.maximum_stock,
                i.unit,
                i.used_stock,
                ing.name as ingredient_name
            FROM inventory i
            LEFT JOIN ingredients ing ON i.menu_item_id = ing.id
            ORDER BY i.current_stock ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $lowStockItems = [];
    foreach ($items as $item) {
        $currentStock = floatval($item['current_stock']);
        $minimumStock = floatval($item['minimum_stock']) ?: 10;
        
        // Match JavaScript logic: critical if at or below minimum, low if at or below 1.5x minimum
        $isCritical = $currentStock <= $minimumStock;
        $isLow = $currentStock <= ($minimumStock * 1.5);
        
        if ($isCritical || $isLow) {
            $item['is_critical'] = $isCritical;
            $lowStockItems[] = $item;
        }
    }
    
    // Create array exactly like the documentation example
    $a = array();
    
    // Header
    $obj1 = new stdClass();
    $obj1->type = 0;
    $obj1->content = 'SIP & SERVE CAFE';
    $obj1->bold = 2;
    $obj1->align = 1;
    $obj1->format = 2;
    array_push($a, $obj1);

    $obj2 = new stdClass();
    $obj2->type = 0;
    $obj2->content = 'SHOPPING LIST';
    $obj2->bold = 1;
    $obj2->align = 1;
    $obj2->format = 1;
    array_push($a, $obj2);

    $obj3 = new stdClass();
    $obj3->type = 0;
    $obj3->content = '================================';
    $obj3->bold = 0;
    $obj3->align = 1;
    $obj3->format = 0;
    array_push($a, $obj3);

    $obj4 = new stdClass();
    $obj4->type = 0;
    $obj4->content = 'Generated: ' . date('M d, Y H:i');
    $obj4->bold = 0;
    $obj4->align = 1;
    $obj4->format = 0;
    array_push($a, $obj4);

    $obj5 = new stdClass();
    $obj5->type = 0;
    $obj5->content = '--------------------------------';
    $obj5->bold = 0;
    $obj5->align = 0;
    $obj5->format = 0;
    array_push($a, $obj5);

    if (empty($lowStockItems)) {
        // No items need restocking
        $objEmpty = new stdClass();
        $objEmpty->type = 0;
        $objEmpty->content = 'No items need restocking';
        $objEmpty->bold = 1;
        $objEmpty->align = 1;
        $objEmpty->format = 0;
        array_push($a, $objEmpty);

        $objGood = new stdClass();
        $objGood->type = 0;
        $objGood->content = 'All inventory levels are good!';
        $objGood->bold = 0;
        $objGood->align = 1;
        $objGood->format = 0;
        array_push($a, $objGood);
    } else {
        // Items header
        $objHeader = new stdClass();
        $objHeader->type = 0;
        $objHeader->content = 'ITEMS TO RESTOCK (' . count($lowStockItems) . ')';
        $objHeader->bold = 1;
        $objHeader->align = 1;
        $objHeader->format = 0;
        array_push($a, $objHeader);

        $objSep = new stdClass();
        $objSep->type = 0;
        $objSep->content = '--------------------------------';
        $objSep->bold = 0;
        $objSep->align = 0;
        $objSep->format = 0;
        array_push($a, $objSep);

        // List each item
        foreach ($lowStockItems as $index => $item) {
            $itemName = $item['ingredient_name'] ?: ('Item ID: ' . $item['menu_item_id']);
            $currentStock = $item['current_stock'];
            $unit = $item['unit'] ?: 'units';
            
            // Match JavaScript calculation logic
            $neededAmount = $item['is_critical'] 
                ? max($currentStock * 3, 10)
                : max($currentStock * 2, 5);

            $priority = $item['is_critical'] ? 'URGENT' : 'LOW';

            // Item number and name
            $objItem = new stdClass();
            $objItem->type = 0;
            $objItem->content = ($index + 1) . '. ' . substr($itemName, 0, 22);
            $objItem->bold = 1;
            $objItem->align = 0;
            $objItem->format = 0;
            array_push($a, $objItem);

            // Current stock
            $objCurrent = new stdClass();
            $objCurrent->type = 0;
            $objCurrent->content = '   Current: ' . number_format($currentStock, 1) . ' ' . $unit;
            $objCurrent->bold = 0;
            $objCurrent->align = 0;
            $objCurrent->format = 0;
            array_push($a, $objCurrent);

            // Needed amount
            $objNeeded = new stdClass();
            $objNeeded->type = 0;
            $objNeeded->content = '   Need: ' . number_format($neededAmount, 1) . ' ' . $unit . ' (' . $priority . ')';
            $objNeeded->bold = 0;
            $objNeeded->align = 0;
            $objNeeded->format = 0;
            array_push($a, $objNeeded);

            // Space between items
            if ($index < count($lowStockItems) - 1) {
                $objSpace = new stdClass();
                $objSpace->type = 0;
                $objSpace->content = '';
                $objSpace->bold = 0;
                $objSpace->align = 0;
                $objSpace->format = 0;
                array_push($a, $objSpace);
            }
        }

        // Summary
        $objSep2 = new stdClass();
        $objSep2->type = 0;
        $objSep2->content = '--------------------------------';
        $objSep2->bold = 0;
        $objSep2->align = 0;
        $objSep2->format = 0;
        array_push($a, $objSep2);

        $criticalCount = count(array_filter($lowStockItems, function($item) {
            return $item['is_critical'];
        }));
        
        $lowCount = count($lowStockItems) - $criticalCount;

        $objSummary = new stdClass();
        $objSummary->type = 0;
        $objSummary->content = 'SUMMARY';
        $objSummary->bold = 1;
        $objSummary->align = 1;
        $objSummary->format = 0;
        array_push($a, $objSummary);

        if ($criticalCount > 0) {
            $objCritical = new stdClass();
            $objCritical->type = 0;
            $objCritical->content = 'Critical Items: ' . $criticalCount;
            $objCritical->bold = 1;
            $objCritical->align = 0;
            $objCritical->format = 0;
            array_push($a, $objCritical);
        }

        if ($lowCount > 0) {
            $objLow = new stdClass();
            $objLow->type = 0;
            $objLow->content = 'Low Stock Items: ' . $lowCount;
            $objLow->bold = 0;
            $objLow->align = 0;
            $objLow->format = 0;
            array_push($a, $objLow);
        }

        $objTotal = new stdClass();
        $objTotal->type = 0;
        $objTotal->content = 'Total Items: ' . count($lowStockItems);
        $objTotal->bold = 1;
        $objTotal->align = 0;
        $objTotal->format = 0;
        array_push($a, $objTotal);
    }

    // Footer
    $objFooter1 = new stdClass();
    $objFooter1->type = 0;
    $objFooter1->content = '================================';
    $objFooter1->bold = 0;
    $objFooter1->align = 1;
    $objFooter1->format = 0;
    array_push($a, $objFooter1);

    $objFooter2 = new stdClass();
    $objFooter2->type = 0;
    $objFooter2->content = 'L PRIMERO CAFE';
    $objFooter2->bold = 0;
    $objFooter2->align = 1;
    $objFooter2->format = 0;
    array_push($a, $objFooter2);

    $objFooter3 = new stdClass();
    $objFooter3->type = 0;
    $objFooter3->content = 'Inventory Management';
    $objFooter3->bold = 0;
    $objFooter3->align = 1;
    $objFooter3->format = 4;
    array_push($a, $objFooter3);
    
    $objFooter4 = new stdClass();
    $objFooter4->type = 0;
    $objFooter4->content = 'Printed: ' . date('Y-m-d H:i:s');
    $objFooter4->bold = 0;
    $objFooter4->align = 1;
    $objFooter4->format = 4;
    array_push($a, $objFooter4);

    // Output exactly like the documentation
    ob_clean();
    echo json_encode($a, JSON_FORCE_OBJECT);
    
} catch (Exception $e) {
    // Error response using stdClass like receipt
    $a = array();
    
    $objErr1 = new stdClass();
    $objErr1->type = 0;
    $objErr1->content = 'Shopping List Error';
    $objErr1->bold = 1;
    $objErr1->align = 1;
    $objErr1->format = 0;
    array_push($a, $objErr1);
    
    $objErr2 = new stdClass();
    $objErr2->type = 0;
    $objErr2->content = 'Database connection failed';
    $objErr2->bold = 0;
    $objErr2->align = 1;
    $objErr2->format = 0;
    array_push($a, $objErr2);
    
    $objErr3 = new stdClass();
    $objErr3->type = 0;
    $objErr3->content = 'Please check server settings';
    $objErr3->bold = 0;
    $objErr3->align = 1;
    $objErr3->format = 0;
    array_push($a, $objErr3);
    
    ob_clean();
    echo json_encode($a, JSON_FORCE_OBJECT);
}

exit;
?>