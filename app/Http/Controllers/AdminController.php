<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AdminController extends Controller
{
    public function userManagement()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Access denied - Admin role required');
        }
        return view('admin.user-management');
    }

    public function createUser(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'role' => 'required|in:admin,manager,cashier,kitchen,customer',
                'status' => 'required|in:active,inactive',
                'password' => 'required|string|min:8',
                'password_confirmation' => 'required|same:password',
                'permissions' => 'nullable|string'
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'status' => $request->status,
                'password' => Hash::make($request->password),
                'permissions' => $request->permissions,
                'email_verified_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUsersData()
    {
        try {
            $users = User::select([
                'id',
                'name',
                'email',
                'role',
                'status',
                'permissions',
                'last_login_at',
                'created_at',
                'updated_at'
            ])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load users: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    // Keep all other methods the same (deleteUser, resetPassword, etc.)
    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);
            if ($user->email === 'laurenceayo7@gmail.com') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete main administrator account'
                ], 403);
            }
            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function resetPassword($id)
    {
        try {
            $user = User::findOrFail($id);
            $newPassword = 'temp' . rand(1000, 9999);
            $user->update([
                'password' => Hash::make($newPassword),
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully',
                'temp_password' => $newPassword
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password: ' . $e->getMessage()
            ], 500);
        }
    }
    public function backupSettings()
    {
        if (Auth::user()->email !== 'laurenceayo7@gmail.com') {
            abort(403, 'Access denied');
        }

        try {
            $backupData = [
                'users' => DB::table('users')->get(),
                'ingredients' => DB::table('ingredients')->get(),
                'menu_items' => DB::table('menu_items')->get(),
                'orders' => DB::table('orders')->get(),
                'order_items' => DB::table('order_items')->get(),
                'categories' => DB::table('categories')->get(),
                'inventory' => DB::table('inventory')->get(),
                'settings' => DB::table('settings')->get(),
                'backup_date' => now()->toDateTimeString(),
                'version' => '1.0'
            ];

            $filename = 'cafe_backup_' . now()->format('Y_m_d_H_i_s') . '.json';

            return response()->json($backupData)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'role' => 'required|in:admin,manager,cashier,kitchen,customer',
                'password' => 'nullable|string|min:8',
            ]);

            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->role = $validated['role'];

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user->fresh()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }
}
