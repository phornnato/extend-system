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
            // Log incoming request
            \Log::info('Register request:', $request->all());

            // Basic validation without database unique checks first
            $email = $request->input('email');
            $username = $request->input('username');
            $password = $request->input('password');
            $passwordConfirm = $request->input('password_confirmation');

            // Manual validation
            if (!$email || !$username || !$password || !$passwordConfirm) {
                return response()->json([
                    'message' => 'All fields are required',
                ], 422);
            }

            if (strlen($username) < 2) {
                return response()->json([
                    'message' => 'Username must be at least 2 characters',
                ], 422);
            }

            if (strlen($password) < 8) {
                return response()->json([
                    'message' => 'Password must be at least 8 characters',
                ], 422);
            }

            if ($password !== $passwordConfirm) {
                return response()->json([
                    'message' => 'Passwords do not match',
                ], 422);
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'message' => 'Invalid email format',
                ], 422);
            }

            // Check if user exists
            $existingUser = User::where('email', $email)->orWhere('name', $username)->first();
            if ($existingUser) {
                return response()->json([
                    'message' => 'Email or username already exists',
                ], 422);
            }

            \Log::info('Creating user...');

            // Create user
            $user = User::create([
                'name' => $username,
                'email' => $email,
                'password' => Hash::make($password),
            ]);

            \Log::info('User created:', $user->toArray());

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            \Log::info('Token generated');

            return response()->json([
                'message' => 'User registered successfully',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token,
            ], 201);
        } catch (\Throwable $e) {
            \Log::error('Register error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return response()->json([
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function login(Request $request)
    {
        try {
            \Log::info('Login request:', $request->all());

            $email = $request->input('email');
            $password = $request->input('password');

            if (!$email || !$password) {
                return response()->json([
                    'message' => 'Email and password are required',
                ], 422);
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'message' => 'Invalid email format',
                ], 422);
            }

            if (strlen($password) < 8) {
                return response()->json([
                    'message' => 'Invalid email or password',
                ], 401);
            }

            \Log::info('Finding user...');

            // Find user
            $user = User::where('email', $email)->first();

            if (!$user) {
                \Log::info('User not found');
                return response()->json([
                    'message' => 'Invalid email or password',
                ], 401);
            }

            // Check password
            if (!Hash::check($password, $user->password)) {
                \Log::info('Password mismatch');
                return response()->json([
                    'message' => 'Invalid email or password',
                ], 401);
            }

            \Log::info('User authenticated');

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            \Log::info('Token generated');

            return response()->json([
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => $token,
            ], 200);
        } catch (\Throwable $e) {
            \Log::error('Login error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return response()->json([
                'message' => $e->getMessage(),
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
