<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;

class SalesController extends Controller
{
    public function index()
    {
        $today = now()->format('Y-m-d');

        // Get actual today's sales from orders table
        $todaysSales = Order::whereDate('created_at', $today)
            ->where('status', 'pending') // Change from 'completed' to 'pending' since your orders start as pending
            ->selectRaw('COUNT(*) as total_orders, SUM(total_amount) as total_sales')
            ->first();

        // Handle null case
        if (!$todaysSales || $todaysSales->total_sales === null) {
            $todaysSales = (object) [
                'total_orders' => 0,
                'total_sales' => 0
            ];
        }

        // Calculate average order
        $averageOrder = $todaysSales->total_orders > 0 
            ? $todaysSales->total_sales / $todaysSales->total_orders 
            : 0;

        // Get actual top selling items
        $formattedTopItems = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->select(
                'menu_items.name',
                DB::raw('SUM(order_items.quantity) as quantity'),
                DB::raw('SUM(order_items.total_price) as revenue')
            )
            ->whereDate('orders.created_at', $today)
            ->where('orders.status', 'pending')
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderBy('quantity', 'desc')
            ->limit(5)
            ->get();

        return view('sales', compact('todaysSales', 'averageOrder', 'formattedTopItems'));
    }
}