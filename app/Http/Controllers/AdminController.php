<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Display the admin user management panel
     */
    public function userManagement()
    {
        // Double check admin access
        if (Auth::user()->email !== 'laurenceayo7@gmail.com') {
            abort(403, 'Access denied');
        }

        return view('admin.user-management');
    }

    /**
     * Get users data for AJAX
     */

    /**
     * Create a new user
     */
    public function createUser(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',  // Changed from first_name/last_name
                'email' => 'required|email|unique:users,email',
                'role' => 'required|in:admin,manager,cashier,kitchen',
                'status' => 'required|in:active,inactive',
                'password' => 'required|string|min:8',
                'password_confirmation' => 'required|same:password',
                'permissions' => 'nullable|string'  // Changed to string to match JS
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'status' => $request->status,
                'password' => Hash::make($request->password),
                'permissions' => $request->permissions,
                'password_reset_required' => false,
                'email_verified_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user  // Changed from 'user' to 'data'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',  // Changed from first_name/last_name
                'email' => 'required|email|unique:users,email,' . $id,
                'role' => 'required|in:admin,manager,cashier,kitchen',
                'status' => 'required|in:active,inactive',
                'password' => 'nullable|string|min:8',
                'permissions' => 'nullable|string'
            ]);

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'status' => $request->status,
                'permissions' => $request->permissions,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user  // Changed from 'user' to 'data'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete user
     */
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

    /**
     * Reset user password
     */
    public function resetPassword($id)
    {
        try {
            $user = User::findOrFail($id);

            $newPassword = $this->generateRandomPassword();

            $user->update([
                'password' => Hash::make($newPassword),
                'password_reset_required' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully',
                'temp_password' => $newPassword  // Changed from 'new_password' to match JS
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user details
     */
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

            Log::info('getUsersData: Found ' . $users->count() . ' users');

            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            Log::error('getUsersData error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load users: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Determine user role based on business logic
     */
    private function getUserRole($user)
    {
        // Add your logic here to determine role
        // For example, based on email, manager_id, or created_by

        if ($user->email === 'laurenceayo7@gmail.com') {
            return 'admin';
        }

        if ($user->manager_id) {
            return 'manager';
        }

        // Default role
        return 'cashier';
    }

    private function generateRandomPassword($length = 8)
    {
        return 'temp' . rand(1000, 9999);  // Simple temporary password format
    }

    /**
     * Get single user details
     */
    public function getUserDetails($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $this->getUserRole($user),
                    'status' => $user->email_verified_at ? 'active' : 'inactive',
                    'permissions' => '',
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                    'created_by' => $user->created_by,
                    'manager_id' => $user->manager_id,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found: ' . $e->getMessage()
            ], 404);
        }
    }
}
