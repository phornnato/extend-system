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

// Protected Routes (requires authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth Routes
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'profile']);
    Route::put('profile', [AuthController::class, 'updateProfile']);

    // Categories
    Route::apiResource('categories', CategoryController::class);
    
    // Incomes
    Route::apiResource('incomes', IncomeController::class);
    
    // Expenses
    Route::apiResource('expenses', ExpenseController::class);

    // Reports
    Route::get('reports/daily', [ReportController::class, 'daily']);
    Route::get('reports/weekly', [ReportController::class, 'weekly']);
    Route::get('reports/monthly', [ReportController::class, 'monthly']);
});
