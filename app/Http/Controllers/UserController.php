<?php

// app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class UserController extends Controller
{
    public function userStore(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'user_name' => 'required|string|max:255',
                'user_email' => 'required|email|unique:users,user_email',
                'user_designation' => 'nullable|string',
                'user_role' => 'nullable|string',
            ]);

            $user = User::create($validatedData);

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function userList()
    {
        try {
            $users = User::all();

            return response()->json([
                'users' => $users,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching users',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function userById($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'user' => $user,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching the user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function userUpdate(Request $request, $user_id)
    {
        try {
            // Find the user by the provided ID (user_id), or fail if not found
            $user = User::findOrFail($user_id);
    
            // Validate incoming request data
            $validatedData = $request->validate([
                'user_name' => 'sometimes|string|max:255',
                'user_email' => 'sometimes|nullable|email|unique:users,user_email,' . $user->user_id . ',user_id',
                'user_designation' => 'nullable|string',
                'user_role' => 'nullable|string',
            ]);
    
            // Update the user with the validated data
            $user->update($validatedData);
    
            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    

    public function userDelete($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'message' => 'User deleted successfully',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting the user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
