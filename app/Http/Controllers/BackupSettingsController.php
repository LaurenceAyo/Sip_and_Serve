<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\BackupSetting;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Ingredient;
use App\Models\MenuItem;
use App\Models\Order;


class BackupSettingsController extends Controller
{
    public function backup()
    {
        if (Auth::user()->email !== 'laurenceayo7@gmail.com') {
            abort(403, 'Access denied');
        }
        $backup = [
            'users' => DB::table('users')->get(),
            'ingredients' => DB::table('ingredients')->get(),
            'menu_items' => DB::table('menu_items')->get(),
            'orders' => DB::table('orders')->get(),
            'order_items' => DB::table('order_items')->get(),
            'categories' => DB::table('categories')->get(),
            'inventory' => DB::table('inventory')->get(),
            'settings' =>   DB::table('settings')->get(),
            'backup_date' => now()->format('Y-m-d H:i:s'),
            'version' => '1.0'
        ];

        return response()->json($backup)
            ->header('Content-Disposition', 'attachment; filename="cafe_backup_' . now()->format('Y-m-d_H-i-s') . '.json"');
    }



    public function restore(Request $request)
    {
        if (Auth::user()->email !== 'laurenceayo7@gmail.com') {
            abort(403, 'Access denied');
        }

        $request->validate([
            'backup_file' => 'required|file|mimes:json|max:10240'
        ]);

        $file = $request->file('backup_file');
        $data = json_decode(file_get_contents($file->getPathname()), true);

        // Validate backup file structure
        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['users'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid backup file format'
            ], 400);
        }

        DB::transaction(function () use ($data) {
            // Clear existing data from all tables
            DB::table('users')->truncate();
            DB::table('ingredients')->truncate();
            DB::table('menu_items')->truncate();
            DB::table('orders')->truncate();
            DB::table('order_items')->truncate();
            DB::table('categories')->truncate();
            DB::table('inventory')->truncate();
            DB::table('settings')->truncate();

            // Restore data to all tables
            foreach ($data['users'] as $user) {
                DB::table('users')->insert((array) $user);
            }
            foreach ($data['ingredients'] as $ingredient) {
                DB::table('ingredients')->insert((array) $ingredient);
            }
            foreach ($data['menu_items'] as $item) {
                DB::table('menu_items')->insert((array) $item);
            }
            foreach ($data['orders'] as $order) {
                DB::table('orders')->insert((array) $order);
            }
            foreach ($data['order_items'] ?? [] as $orderItem) {
                DB::table('order_items')->insert((array) $orderItem);
            }
            foreach ($data['categories'] ?? [] as $category) {
                DB::table('categories')->insert((array) $category);
            }
            foreach ($data['inventory'] ?? [] as $inventory) {
                DB::table('inventory')->insert((array) $inventory);
            }
            foreach ($data['settings'] ?? [] as $setting) {
                DB::table('settings')->insert((array) $setting);
            }
        });

        return response()->json(['success' => true]);
    }
    public function index()
    {
        if (Auth::user()->email !== 'laurenceayo7@gmail.com') {
            abort(403, 'Access denied');
        }

        $settings = BackupSetting::first();

        return view('admin.backup-settings', compact('settings'));
    }

    public function update(Request $request)
    {
        if (Auth::user()->email !== 'laurenceayo7@gmail.com') {
            abort(403, 'Access denied');
        }

        $request->validate([
            'backup_location' => 'required|in:local,server',
            'backup_schedule' => 'required|in:weekly,monthly',
            'auto_backup_enabled' => 'boolean'
        ]);

        $settings = BackupSetting::firstOrCreate();
        $settings->update([
            'backup_location' => $request->backup_location,
            'backup_schedule' => $request->backup_schedule,
            'auto_backup_enabled' => $request->has('auto_backup_enabled'),
            'next_backup_at' => $settings->calculateNextBackup()
        ]);

        return redirect()->back()->with('success', 'Backup settings updated successfully!');
    }

    public function generateBackupData()
    {
        return [
            'users' => DB::table('users')->get(),
            'ingredients' => DB::table('ingredients')->get(),
            'menu_items' => DB::table('menu_items')->get(),
            'orders' => DB::table('orders')->get(),
            'order_items' => DB::table('order_items')->get(),
            'categories' => DB::table('categories')->get(),
            'inventory' => DB::table('inventory')->get(),
            'settings' => DB::table('settings')->get(),
            'backup_date' => now()->format('Y-m-d H:i:s'),
            'version' => '1.0'
        ];
    }
}
