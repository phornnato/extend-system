<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'username' => 'required|string|min:2|max:255|unique:users,name',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'password_confirmation' => 'required|same:password',
            ]);

            // Create user
            $user = User::create([
                'name' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User registered successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = [];
            foreach ($e->errors() as $field => $messages) {
                $errors[$field] = $messages[0];
            }
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $errors,
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function login(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:8',
            ]);

            // Find user
            $user = User::where('email', $validated['email'])->first();

            // Check password
            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return response()->json([
                    'message' => 'Invalid email or password',
                ], 401);
            }

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = [];
            foreach ($e->errors() as $field => $messages) {
                $errors[$field] = $messages[0];
            }
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $errors,
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Login failed',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'message' => 'Not authenticated',
                ], 401);
            }

            $user->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Logged out successfully',
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Logout failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function profile(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'message' => 'Not authenticated',
                ], 401);
            }

            return response()->json([
                'user' => $user,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to fetch profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'message' => 'Not authenticated',
                ], 401);
            }
            
            $validated = $request->validate([
                'username' => 'nullable|string|min:2|max:255|unique:users,name,' . $user->id,
                'email' => 'nullable|email|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:8',
                'password_confirmation' => 'nullable|same:password',
            ]);

            if (isset($validated['username'])) {
                $user->name = $validated['username'];
            }

            if (isset($validated['email'])) {
                $user->email = $validated['email'];
            }

            if (isset($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => $user,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Profile update failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
