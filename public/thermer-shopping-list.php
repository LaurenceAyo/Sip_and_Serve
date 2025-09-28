<?php
// thermer-shopping-list.php - Corrected for actual database structure
header('Content-Type: application/json');
header('Cache-Control: no-cache');

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
    
    // Database connection
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $database = $_ENV['DB_DATABASE'] ?? '';
    $username = $_ENV['DB_USERNAME'] ?? '';
    $password = $_ENV['DB_PASSWORD'] ?? '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Query inventory with ingredients based on your actual structure
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
            WHERE i.current_stock > 0
            ORDER BY i.current_stock ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate low stock items
    $lowStockItems = [];
    foreach ($items as $item) {
        $currentStock = floatval($item['current_stock']);
        $minimumStock = floatval($item['minimum_stock']) ?: 10;
        
        $isCritical = $currentStock <= $minimumStock;
        $isLow = $currentStock <= ($minimumStock * 1.5);
        
        if ($isCritical || $isLow) {
            $item['calculated_status'] = $isCritical ? 'critical' : 'low';
            $lowStockItems[] = $item;
        }
    }
    
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

    // Date and time
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
        $obj_empty = new stdClass();
        $obj_empty->type = 0;
        $obj_empty->content = 'No items need restocking';
        $obj_empty->bold = 1;
        $obj_empty->align = 1;
        $obj_empty->format = 0;
        array_push($a, $obj_empty);

        $obj_good = new stdClass();
        $obj_good->type = 0;
        $obj_good->content = 'All inventory levels are good!';
        $obj_good->bold = 0;
        $obj_good->align = 1;
        $obj_good->format = 0;
        array_push($a, $obj_good);
    } else {
        // Items header
        $obj_header = new stdClass();
        $obj_header->type = 0;
        $obj_header->content = 'ITEMS TO RESTOCK (' . count($lowStockItems) . ')';
        $obj_header->bold = 1;
        $obj_header->align = 1;
        $obj_header->format = 0;
        array_push($a, $obj_header);

        $obj_sep = new stdClass();
        $obj_sep->type = 0;
        $obj_sep->content = '--------------------------------';
        $obj_sep->bold = 0;
        $obj_sep->align = 0;
        $obj_sep->format = 0;
        array_push($a, $obj_sep);

        // List each item
        foreach ($lowStockItems as $index => $item) {
            $itemName = $item['ingredient_name'] ?: ('Item ID: ' . $item['menu_item_id']);
            $currentStock = number_format($item['current_stock'], 1);
            $unit = $item['unit'] ?: 'units';
            
            // Calculate needed amount
            $neededAmount = $item['calculated_status'] === 'critical' 
                ? max($item['current_stock'] * 3, 10)
                : max($item['current_stock'] * 2, 5);
            $neededFormatted = number_format($neededAmount, 1);

            $priority = $item['calculated_status'] === 'critical' ? 'URGENT' : 'LOW';

            // Item number and name (truncate to fit thermal printer)
            $obj_item = new stdClass();
            $obj_item->type = 0;
            $obj_item->content = ($index + 1) . '. ' . substr($itemName, 0, 22);
            $obj_item->bold = 1;
            $obj_item->align = 0;
            $obj_item->format = 0;
            array_push($a, $obj_item);

            // Current stock
            $obj_current = new stdClass();
            $obj_current->type = 0;
            $obj_current->content = '   Current: ' . $currentStock . ' ' . $unit;
            $obj_current->bold = 0;
            $obj_current->align = 0;
            $obj_current->format = 0;
            array_push($a, $obj_current);

            // Needed amount
            $obj_needed = new stdClass();
            $obj_needed->type = 0;
            $obj_needed->content = '   Need: ' . $neededFormatted . ' ' . $unit . ' (' . $priority . ')';
            $obj_needed->bold = 0;
            $obj_needed->align = 0;
            $obj_needed->format = 0;
            array_push($a, $obj_needed);

            // Space between items
            if ($index < count($lowStockItems) - 1) {
                $obj_space = new stdClass();
                $obj_space->type = 0;
                $obj_space->content = '';
                $obj_space->bold = 0;
                $obj_space->align = 0;
                $obj_space->format = 0;
                array_push($a, $obj_space);
            }
        }

        // Summary
        $obj_sep2 = new stdClass();
        $obj_sep2->type = 0;
        $obj_sep2->content = '--------------------------------';
        $obj_sep2->bold = 0;
        $obj_sep2->align = 0;
        $obj_sep2->format = 0;
        array_push($a, $obj_sep2);

        $criticalCount = count(array_filter($lowStockItems, function($item) {
            return $item['calculated_status'] === 'critical';
        }));
        
        $lowCount = count(array_filter($lowStockItems, function($item) {
            return $item['calculated_status'] === 'low';
        }));

        $obj_summary = new stdClass();
        $obj_summary->type = 0;
        $obj_summary->content = 'SUMMARY';
        $obj_summary->bold = 1;
        $obj_summary->align = 1;
        $obj_summary->format = 0;
        array_push($a, $obj_summary);

        if ($criticalCount > 0) {
            $obj_critical = new stdClass();
            $obj_critical->type = 0;
            $obj_critical->content = 'Critical Items: ' . $criticalCount;
            $obj_critical->bold = 1;
            $obj_critical->align = 0;
            $obj_critical->format = 0;
            array_push($a, $obj_critical);
        }

        if ($lowCount > 0) {
            $obj_low = new stdClass();
            $obj_low->type = 0;
            $obj_low->content = 'Low Stock Items: ' . $lowCount;
            $obj_low->bold = 0;
            $obj_low->align = 0;
            $obj_low->format = 0;
            array_push($a, $obj_low);
        }

        $obj_total = new stdClass();
        $obj_total->type = 0;
        $obj_total->content = 'Total Items: ' . count($lowStockItems);
        $obj_total->bold = 1;
        $obj_total->align = 0;
        $obj_total->format = 0;
        array_push($a, $obj_total);
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
    $obj_footer2->content = 'L PRIMERO CAFE';
    $obj_footer2->bold = 0;
    $obj_footer2->align = 1;
    $obj_footer2->format = 0;
    array_push($a, $obj_footer2);

    $obj_footer3 = new stdClass();
    $obj_footer3->type = 0;
    $obj_footer3->content = 'Inventory Management';
    $obj_footer3->bold = 0;
    $obj_footer3->align = 1;
    $obj_footer3->format = 0;
    array_push($a, $obj_footer3);

    echo json_encode($a, JSON_FORCE_OBJECT);

} catch (Exception $e) {
    $response = [
        [
            'type' => 0,
            'content' => 'Error: ' . $e->getMessage(),
            'bold' => 1,
            'align' => 1,
            'format' => 0
        ]
    ];
    echo json_encode($response, JSON_FORCE_OBJECT);
}
?>