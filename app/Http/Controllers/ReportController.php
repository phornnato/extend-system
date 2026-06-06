<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function daily(Request $request)
    {
        try {
            if (!$request->user()) {
                return response()->json(['message' => 'Unauthorized - no user', 'error' => 'Please login first'], 401);
            }
            $userId = $request->user()->id;
            $today = Carbon::today()->toDateString();
            
            $income = Income::where('user_id', $userId)
                ->whereDate('date', $today)
                ->sum('amount');
            $expense = Expense::where('user_id', $userId)
                ->whereDate('date', $today)
                ->sum('amount');
            
            return response()->json([
                'report_type' => 'daily',
                'date' => $today,
                'total_income' => (float)$income,
                'total_expense' => (float)$expense,
                'balance' => (float)($income - $expense)
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Report daily error', 'error' => $e->getMessage()], 500);
        }
    }

    public function weekly(Request $request)
    {
        try {
            if (!$request->user()) {
                return response()->json(['message' => 'Unauthorized - no user', 'error' => 'Please login first'], 401);
            }
            $userId = $request->user()->id;
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            
            $income = Income::where('user_id', $userId)
                ->whereBetween('date', [$startOfWeek, $endOfWeek])
                ->sum('amount');
            $expense = Expense::where('user_id', $userId)
                ->whereBetween('date', [$startOfWeek, $endOfWeek])
                ->sum('amount');
            
            return response()->json([
                'report_type' => 'weekly',
                'start_date' => $startOfWeek->toDateString(),
                'end_date' => $endOfWeek->toDateString(),
                'total_income' => (float)$income,
                'total_expense' => (float)$expense,
                'balance' => (float)($income - $expense)
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Report weekly error', 'error' => $e->getMessage()], 500);
        }
    }

    public function monthly(Request $request)
    {
        try {
            if (!$request->user()) {
                return response()->json(['message' => 'Unauthorized - no user', 'error' => 'Please login first'], 401);
            }
            $userId = $request->user()->id;
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            
            $income = Income::where('user_id', $userId)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->sum('amount');
            $expense = Expense::where('user_id', $userId)
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->sum('amount');
            
            return response()->json([
                'report_type' => 'monthly',
                'month' => Carbon::now()->format('F Y'),
                'total_income' => (float)$income,
                'total_expense' => (float)$expense,
                'balance' => (float)($income - $expense)
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Report monthly error', 'error' => $e->getMessage()], 500);
        }
    }
}
