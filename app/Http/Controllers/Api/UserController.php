<?php
// app/Http/Controllers/Api/UserController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'success' => true,
                'data' => $users,
                'message' => 'Users retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving users: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:admin,manager,cashier,kitchen',
            'status' => 'required|string|in:active,inactive',
            'permissions' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password, // Will be hashed by mutator
                'role' => $request->role,
                'status' => $request->status,
                'permissions' => $request->permissions ?? '[]',
                'email_verified_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found: ' . $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|in:admin,manager,cashier,kitchen',
            'status' => 'required|string|in:active,inactive',
            'permissions' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::findOrFail($id);
            
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'status' => $request->status,
                'permissions' => $request->permissions ?? '[]'
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = $request->password; // Will be hashed by mutator
            }

            $user->update($updateData);

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Prevent deletion of main admin
            if ($user->email === 'laurenceayo7@gmail.com') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete the main administrator account'
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
                'message' => 'Error deleting user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function resetPassword($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Generate temporary password
            $tempPassword = Str::random(10);
            
            $user->update([
                'password' => $tempPassword, // Will be hashed by mutator
                'password_reset_required' => true
            ]);

            return response()->json([
                'success' => true,
                'temp_password' => $tempPassword,
                'message' => 'Password reset successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error resetting password: ' . $e->getMessage()
            ], 500);
        }
    }
}