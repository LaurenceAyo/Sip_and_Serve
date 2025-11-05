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

        // FIXED: Get from daily_sales table with proper total calculation
        // The total_amount in daily_sales should already reflect discounts from cashier
        $todaysSales = DB::table('daily_sales')
            ->whereBetween('completion_time', [$startDate, $endDate])
            ->selectRaw('COUNT(*) as total_orders, SUM(total_amount) as total_sales')
            ->first();

        if (!$todaysSales || $todaysSales->total_sales === null) {
            $todaysSales = (object) ['total_orders' => 0, 'total_sales' => 0];
        }

        // Calculate discount statistics
        $discountStats = DB::table('orders')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->selectRaw('
                COUNT(CASE WHEN discount_type IS NOT NULL AND discount_type != "none" THEN 1 END) as discount_count,
                SUM(CASE WHEN discount_amount > 0 THEN discount_amount ELSE 0 END) as total_discounts,
                SUM(CASE WHEN discount_type = "senior_citizen" THEN discount_amount ELSE 0 END) as senior_discounts,
                SUM(CASE WHEN discount_type = "pwd" THEN discount_amount ELSE 0 END) as pwd_discounts
            ')
            ->first();

        $averageOrder = $todaysSales->total_orders > 0
            ? $todaysSales->total_sales / $todaysSales->total_orders
            : 0;

        // Get top items from completed orders only
        $TopItems = DB::table('order_items')
            ->join('daily_sales', 'order_items.order_id', '=', 'daily_sales.order_id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->select(
                'menu_items.name',
                DB::raw('SUM(order_items.quantity) as quantity'),
                DB::raw('SUM(order_items.total_price) as revenue')
            )
            ->whereBetween('daily_sales.completion_time', [$startDate, $endDate])
            ->groupBy('menu_items.id', 'menu_items.name')
            ->orderBy('quantity', 'desc')
            ->limit(5)
            ->get();

        return view('sales', compact(
            'todaysSales', 
            'averageOrder', 
            'TopItems', 
            'filter',
            'discountStats'
        ));
    }
}