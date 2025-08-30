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
    public function getUsersData()
    {
        $users = User::select('id', 'name', 'email', 'role', 'status', 'last_login_at', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }

    /**
     * Create a new user
     */
    public function createUser(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,manager,cashier,kitchen',
            'status' => 'required|in:active,inactive',
            'password' => 'required|string|min:8|confirmed',
            'permissions' => 'array'
        ]);

        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'role' => $request->role,
            'status' => $request->status,
            'password' => Hash::make($request->password),
            'permissions' => $request->permissions ?? [],
            'created_by' => Auth::id(),
            'email_verified_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'user' => $user
        ]);
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,manager,cashier,kitchen',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:8'
        ]);

        $updateData = [
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'role' => $request->role,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
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
    }

    /**
     * Reset user password
     */
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        
        $newPassword = $this->generateRandomPassword();
        
        $user->update([
            'password' => Hash::make($newPassword),
            'password_reset_required' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully',
            'new_password' => $newPassword
        ]);
    }

    /**
     * Get user details
     */
    public function getUserDetails($id)
    {
        $user = User::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
                'created_at' => $user->created_at,
                'last_login_at' => $user->last_login_at,
                'permissions' => $user->permissions ?? []
            ]
        ]);
    }

    private function generateRandomPassword($length = 12)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $password;
    }
}