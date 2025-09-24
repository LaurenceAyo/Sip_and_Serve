<?php
// app/Helpers/NavigationHelper.php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use App\Models\Role;

class NavigationHelper
{
    public static function getNavigationItems()
    {
        if (!Auth::check()) {
            return [];
        }

        $user = Auth::user();
        $items = [];

        // Cashier Access
        if ($user->canAccessCashier()) {
            $items[] = [
                'name' => 'Cashier',
                'route' => 'cashier.index',
                'icon' => 'cash-register',
                'description' => 'Process orders and payments'
            ];
        }

        // Kitchen Access
        if ($user->canAccessKitchen()) {
            $items[] = [
                'name' => 'Kitchen',
                'route' => 'kitchen.index',
                'icon' => 'chef-hat',
                'description' => 'Manage kitchen orders'
            ];
        }

        // Dashboard Access (Manager/Admin)
        if ($user->canAccessDashboard()) {
            $items[] = [
                'name' => 'Dashboard',
                'route' => 'dashboard',
                'icon' => 'chart-bar',
                'description' => 'Overview and inventory'
            ];
        }

        // Sales Access (Manager/Admin)
        if ($user->canAccessSales()) {
            $items[] = [
                'name' => 'Sales',
                'route' => 'sales',
                'icon' => 'trending-up',
                'description' => 'Sales reports and analytics'
            ];
        }

        // Product Access (Manager/Admin)
        if ($user->canAccessProduct()) {
            $items[] = [
                'name' => 'Products',
                'route' => 'product',
                'icon' => 'package',
                'description' => 'Manage menu items and products'
            ];
        }

        // Admin Access (Admin only)
        if ($user->canAccessAdmin()) {
            $items[] = [
                'name' => 'Admin Panel',
                'route' => 'admin.users',
                'icon' => 'settings',
                'description' => 'User management and system settings'
            ];
        }

        return $items;
    }

    public static function getQuickActions()
    {
        if (!Auth::check()) {
            return [];
        }

        $user = Auth::user();
        $actions = [];

        if ($user->canAccessCashier()) {
            $actions[] = [
                'name' => 'New Order',
                'route' => 'cashier.index',
                'icon' => 'plus-circle',
                'class' => 'bg-green-500 hover:bg-green-600'
            ];
        }

        if ($user->canAccessKitchen()) {
            $actions[] = [
                'name' => 'Kitchen Orders',
                'route' => 'kitchen.index',
                'icon' => 'clock',
                'class' => 'bg-orange-500 hover:bg-orange-600'
            ];
        }

        if ($user->canAccessSales()) {
            $actions[] = [
                'name' => 'Sales Report',
                'route' => 'sales',
                'icon' => 'chart-line',
                'class' => 'bg-blue-500 hover:bg-blue-600'
            ];
        }

        return $actions;
    }

    public static function getRoleBasedRedirect()
    {
        if (!Auth::check()) {
            return route('login');
        }

        $user = Auth::user();

        // Administrator - redirect to admin panel
        if ($user->isAdministrator()) {
            return route('admin.users');
        }

        // Manager - redirect to dashboard
        if ($user->isManager()) {
            return route('dashboard');
        }

        // Cashier - redirect to cashier interface
        if ($user->isCashier()) {
            return route('cashier.index');
        }

        // Kitchen Staff - redirect to kitchen interface
        if ($user->isKitchenStaff()) {
            return route('kitchen.index');
        }

        // Default fallback
        return route('profile.edit');
    }
}