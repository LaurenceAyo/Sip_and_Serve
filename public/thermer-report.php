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
    // Get the report date from URL parameter (if provided)
    $reportDate = isset($_GET['date']) ? $_GET['date'] : date('F d, Y');
    
    // Get report data from URL parameters
    $totalRevenue = isset($_GET['revenue']) ? $_GET['revenue'] : '0.00';
    $totalOrders = isset($_GET['orders']) ? intval($_GET['orders']) : 0;
    $totalItems = isset($_GET['items']) ? intval($_GET['items']) : 0;
    $avgOrderValue = isset($_GET['avg']) ? $_GET['avg'] : '0.00';
    
    // Get top items data (passed as JSON string)
    $topItemsJson = isset($_GET['top_items']) ? urldecode($_GET['top_items']) : '[]';
    $topItems = json_decode($topItemsJson, true) ?: [];
    
    // Create array for Thermer format
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
    $obj2->content = 'DAILY SALES REPORT';
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
    $obj4->content = $reportDate;
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

    // Sales Overview Section
    $objSalesHeader = new stdClass();
    $objSalesHeader->type = 0;
    $objSalesHeader->content = 'SALES OVERVIEW';
    $objSalesHeader->bold = 1;
    $objSalesHeader->align = 1;
    $objSalesHeader->format = 0;
    array_push($a, $objSalesHeader);

    $objSpace1 = new stdClass();
    $objSpace1->type = 0;
    $objSpace1->content = '';
    $objSpace1->bold = 0;
    $objSpace1->align = 0;
    $objSpace1->format = 0;
    array_push($a, $objSpace1);

    // Total Revenue
    $objRevLabel = new stdClass();
    $objRevLabel->type = 0;
    $objRevLabel->content = 'Total Revenue:';
    $objRevLabel->bold = 0;
    $objRevLabel->align = 0;
    $objRevLabel->format = 0;
    array_push($a, $objRevLabel);

    $objRevValue = new stdClass();
    $objRevValue->type = 0;
    $objRevValue->content = '₱' . number_format($totalRevenue, 2);
    $objRevValue->bold = 2;
    $objRevValue->align = 1;
    $objRevValue->format = 1;
    array_push($a, $objRevValue);

    $objSpace2 = new stdClass();
    $objSpace2->type = 0;
    $objSpace2->content = '';
    $objSpace2->bold = 0;
    $objSpace2->align = 0;
    $objSpace2->format = 0;
    array_push($a, $objSpace2);

    // Total Orders
    $objOrdersLabel = new stdClass();
    $objOrdersLabel->type = 0;
    $objOrdersLabel->content = 'Total Orders:';
    $objOrdersLabel->bold = 0;
    $objOrdersLabel->align = 0;
    $objOrdersLabel->format = 0;
    array_push($a, $objOrdersLabel);

    $objOrdersValue = new stdClass();
    $objOrdersValue->type = 0;
    $objOrdersValue->content = $totalOrders . ' orders';
    $objOrdersValue->bold = 1;
    $objOrdersValue->align = 1;
    $objOrdersValue->format = 0;
    array_push($a, $objOrdersValue);

    $objSpace3 = new stdClass();
    $objSpace3->type = 0;
    $objSpace3->content = '';
    $objSpace3->bold = 0;
    $objSpace3->align = 0;
    $objSpace3->format = 0;
    array_push($a, $objSpace3);

    // Total Items Sold
    $objItemsLabel = new stdClass();
    $objItemsLabel->type = 0;
    $objItemsLabel->content = 'Total Items Sold:';
    $objItemsLabel->bold = 0;
    $objItemsLabel->align = 0;
    $objItemsLabel->format = 0;
    array_push($a, $objItemsLabel);

    $objItemsValue = new stdClass();
    $objItemsValue->type = 0;
    $objItemsValue->content = $totalItems . ' items';
    $objItemsValue->bold = 1;
    $objItemsValue->align = 1;
    $objItemsValue->format = 0;
    array_push($a, $objItemsValue);

    $objSpace4 = new stdClass();
    $objSpace4->type = 0;
    $objSpace4->content = '';
    $objSpace4->bold = 0;
    $objSpace4->align = 0;
    $objSpace4->format = 0;
    array_push($a, $objSpace4);

    // Average Order Value
    $objAvgLabel = new stdClass();
    $objAvgLabel->type = 0;
    $objAvgLabel->content = 'Average Order Value:';
    $objAvgLabel->bold = 0;
    $objAvgLabel->align = 0;
    $objAvgLabel->format = 0;
    array_push($a, $objAvgLabel);

    $objAvgValue = new stdClass();
    $objAvgValue->type = 0;
    $objAvgValue->content = '₱' . number_format($avgOrderValue, 2);
    $objAvgValue->bold = 1;
    $objAvgValue->align = 1;
    $objAvgValue->format = 0;
    array_push($a, $objAvgValue);

    // Separator
    $objSep1 = new stdClass();
    $objSep1->type = 0;
    $objSep1->content = '--------------------------------';
    $objSep1->bold = 0;
    $objSep1->align = 0;
    $objSep1->format = 0;
    array_push($a, $objSep1);

    // Top Selling Items Section
    if (!empty($topItems)) {
        $objTopHeader = new stdClass();
        $objTopHeader->type = 0;
        $objTopHeader->content = 'TOP SELLING ITEMS';
        $objTopHeader->bold = 1;
        $objTopHeader->align = 1;
        $objTopHeader->format = 0;
        array_push($a, $objTopHeader);

        $objSpace5 = new stdClass();
        $objSpace5->type = 0;
        $objSpace5->content = '';
        $objSpace5->bold = 0;
        $objSpace5->align = 0;
        $objSpace5->format = 0;
        array_push($a, $objSpace5);

        foreach ($topItems as $index => $item) {
            $rank = $index + 1;
            $itemName = isset($item['name']) ? $item['name'] : 'Unknown Item';
            $quantity = isset($item['quantity']) ? intval($item['quantity']) : 0;
            $revenue = isset($item['revenue']) ? floatval($item['revenue']) : 0;

            // Rank and Item Name
            $objItemLine = new stdClass();
            $objItemLine->type = 0;
            $objItemLine->content = $rank . '. ' . substr($itemName, 0, 25);
            $objItemLine->bold = 1;
            $objItemLine->align = 0;
            $objItemLine->format = 0;
            array_push($a, $objItemLine);

            // Quantity Sold
            $objQty = new stdClass();
            $objQty->type = 0;
            $objQty->content = '   Sold: ';
            $objQty->bold = 0;
            $objQty->align = 0;
            $objQty->format = 0;
            array_push($a, $objQty);

            // Revenue
            $objRev = new stdClass();
            $objRev->type = 0;
            $objRev->content = '   Revenue: ₱' . number_format($revenue, 2);
            $objRev->bold = 0;
            $objRev->align = 0;
            $objRev->format = 0;
            array_push($a, $objRev);

            // Space between items
            if ($index < count($topItems) - 1) {
                $objItemSpace = new stdClass();
                $objItemSpace->type = 0;
                $objItemSpace->content = '';
                $objItemSpace->bold = 0;
                $objItemSpace->align = 0;
                $objItemSpace->format = 0;
                array_push($a, $objItemSpace);
            }
        }
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
    $objFooter2->content = 'Generated by Sip & Serve';
    $objFooter2->bold = 0;
    $objFooter2->align = 1;
    $objFooter2->format = 0;
    array_push($a, $objFooter2);

    $objFooter3 = new stdClass();
    $objFooter3->type = 0;
    $objFooter3->content = 'Cafe Management System';
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

    // Output exactly like Thermer expects
    ob_clean();
    echo json_encode($a, JSON_FORCE_OBJECT);
    
} catch (Exception $e) {
    // Error response
    $a = array();
    
    $objErr1 = new stdClass();
    $objErr1->type = 0;
    $objErr1->content = 'Report Generation Error';
    $objErr1->bold = 1;
    $objErr1->align = 1;
    $objErr1->format = 0;
    array_push($a, $objErr1);
    
    $objErr2 = new stdClass();
    $objErr2->type = 0;
    $objErr2->content = 'Failed to generate report';
    $objErr2->bold = 0;
    $objErr2->align = 1;
    $objErr2->format = 0;
    array_push($a, $objErr2);
    
    $objErr3 = new stdClass();
    $objErr3->type = 0;
    $objErr3->content = 'Please try again';
    $objErr3->bold = 0;
    $objErr3->align = 1;
    $objErr3->format = 0;
    array_push($a, $objErr3);
    
    ob_clean();
    echo json_encode($a, JSON_FORCE_OBJECT);
}

exit;
?>