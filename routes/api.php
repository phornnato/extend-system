<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CategoryController;

// Public Auth Routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);
Route::get('profile', [AuthController::class, 'profile']);
Route::put('profile', [AuthController::class, 'updateProfile']);

// Categories (Public)
Route::apiResource('categories', CategoryController::class);

// Incomes (Public)
Route::apiResource('incomes', IncomeController::class);

// Expenses (Public)
Route::apiResource('expenses', ExpenseController::class);

// Reports (Public)
Route::get('reports/daily', [ReportController::class, 'daily']);
Route::get('reports/weekly', [ReportController::class, 'weekly']);
Route::get('reports/monthly', [ReportController::class, 'monthly']);

// Deployment Helper - Run migrations
Route::post('migrate', function () {
    if (env('APP_ENV') !== 'production') {
        return response()->json(['message' => 'Migrations only available in production'], 403);
    }
    
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true, '--no-interaction' => true]);
        return response()->json(['message' => 'Migrations completed', 'output' => \Illuminate\Support\Facades\Artisan::output()], 200);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Migration failed', 'error' => $e->getMessage()], 500);
    }
});
