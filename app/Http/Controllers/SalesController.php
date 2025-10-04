<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'TODAY');

        // Set date range based on filter
        switch ($filter) {
            case 'THIS WEEK':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                break;
            case 'THIS MONTH':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;
            case 'TODAY':
            default:
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
                break;
        }

        // Get sales data for the selected period
        $todaysSales = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->selectRaw('COUNT(*) as total_orders, SUM(total_amount) as total_sales')
            ->first();

        if (!$todaysSales || $todaysSales->total_sales === null) {
            $todaysSales = (object) ['total_orders' => 0, 'total_sales' => 0];
        }

        $averageOrder = $todaysSales->total_orders > 0
            ? $todaysSales->total_sales / $todaysSales->total_orders
            : 0;

        // Changed variable name to match Blade view ($TopItems with capital T)
        $TopItems = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->select(
                'menu_items.name', 
                DB::raw('SUM(order_items.quantity) as quantity'), 
                DB::raw('SUM(order_items.total_price) as revenue')
            )
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'completed')
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderBy('quantity', 'desc')
            ->limit(5)
            ->get();

        // Pass $topItems instead of $formattedTopItems
        return view('sales', compact('todaysSales', 'averageOrder', 'TopItems', 'filter'));
    }
}