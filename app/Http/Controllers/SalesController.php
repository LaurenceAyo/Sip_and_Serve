<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function index()
    {
        // Get today's sales data
        $today = now()->format('Y-m-d');

        // Get today's sales summary from daily_sales
        $todaysSales = DB::table('daily_sales')
            ->where('date', $today)
            ->first();

        // If no data for today, create default values
        if (!$todaysSales) {
            $todaysSales = (object) [
                'total_orders' => 0,
                'total_sales' => 0,
                'cash_sales' => 0,
                'card_sales' => 0,
                'digital_wallet_sales' => 0
            ];
        }

        // Calculate average order value
        $averageOrder = $todaysSales->total_orders > 0
            ? $todaysSales->total_sales / $todaysSales->total_orders
            : 0;

        // Get top selling items from order_items table (today's data)
        $topItems = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                'order_items.menu_item_id',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.total_price) as total_revenue')
            )
            ->whereDate('orders.created_at', $today)
            ->where('orders.status', 'completed') // Only count completed orders
            ->groupBy('order_items.menu_item_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(4)
            ->get();

        // If you have a menu_items table, join it to get item names
        // Otherwise, create a mapping array
        $itemNames = [
            47 => ['name' => 'Pad Thai', 'emoji' => '🍛'],
            3 => ['name' => 'Iced Coffee', 'emoji' => '🥤'],
            27 => ['name' => 'Club Sandwich', 'emoji' => '🥪'],
            // Add more mappings based on your menu_item_ids
        ];

        // Format top items with names and emojis
        $formattedTopItems = [];
        foreach ($topItems as $item) {
            $itemData = $itemNames[$item->menu_item_id] ?? ['name' => 'Item #' . $item->menu_item_id, 'emoji' => '🍽️'];

            $formattedTopItems[] = (object) [
                'name' => $itemData['name'],
                'quantity' => $item->total_quantity,
                'revenue' => $item->total_revenue,
                'emoji' => $itemData['emoji']
            ];
        }
        // Get top selling items with actual menu item names
        $topItems = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->select(
                'menu_items.name',
                'menu_items.category',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.total_price) as total_revenue')
            )
            ->whereDate('orders.created_at', $today)
            ->where('orders.status', 'completed')
            ->groupBy('menu_items.id', 'menu_items.name', 'menu_items.category')
            ->orderBy('total_quantity', 'desc')
            ->limit(4)
            ->get();

        // Add emojis based on category or name
        $categoryEmojis = [
            'coffee' => '☕',
            'food' => '🍛',
            'drinks' => '🥤',
            'sandwich' => '🥪'
        ];

        $formattedTopItems = [];
        foreach ($topItems as $item) {
            $emoji = $categoryEmojis[strtolower($item->category)] ?? '🍽️';

            $formattedTopItems[] = (object) [
                'name' => $item->name,
                'quantity' => $item->total_quantity,
                'revenue' => $item->total_revenue,
                'emoji' => $emoji
            ];
        }

        return view('sales', compact('todaysSales', 'averageOrder', 'formattedTopItems'));
    }
}
