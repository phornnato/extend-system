<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $email = $request->input('email');
            $username = $request->input('username');
            $password = $request->input('password');

            if (!$email || !$username || !$password) {
                return response()->json(['message' => 'Missing fields'], 400);
            }

            // Check if user exists
            $exists = User::where('email', $email)->orWhere('name', $username)->first();
            if ($exists) {
                return response()->json(['message' => 'User already exists'], 400);
            }

            // Create user with simple token
            $token = Str::random(60);
            $user = User::create([
                'name' => $username,
                'email' => $email,
                'password' => Hash::make($password),
                'token' => $token,
            ]);

            return response()->json([
                'message' => 'Registered',
                'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Register error',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $email = $request->input('email');
            $password = $request->input('password');

            if (!$email || !$password) {
                return response()->json(['message' => 'Missing credentials'], 400);
            }

            $user = User::where('email', $email)->first();
            if (!$user || !Hash::check($password, $user->password)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            // Generate new token
            $token = Str::random(60);
            $user->update(['token' => $token]);

            return response()->json([
                'message' => 'Logged in',
                'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
                'token' => $token,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login error',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        return response()->json(['message' => 'Logged out'], 200);
    }

    public function profile(Request $request)
    {
        return response()->json(['message' => 'OK'], 200);
    }

    public function updateProfile(Request $request)
    {
        return response()->json(['message' => 'Updated'], 200);
    }
}
