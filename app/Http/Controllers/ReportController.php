<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function daily()
    {
        $today = Carbon::today()->toDateString();
        
        $income = Income::whereDate('date', $today)->sum('amount');
        $expense = Expense::whereDate('date', $today)->sum('amount');
        
        return response()->json([
            'report_type' => 'daily',
            'date' => $today,
            'total_income' => (float)$income,
            'total_expense' => (float)$expense,
            'balance' => (float)($income - $expense)
        ]);
    }

    public function weekly()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $income = Income::whereBetween('date', [$startOfWeek, $endOfWeek])->sum('amount');
        $expense = Expense::whereBetween('date', [$startOfWeek, $endOfWeek])->sum('amount');
        
        return response()->json([
            'report_type' => 'weekly',
            'start_date' => $startOfWeek->toDateString(),
            'end_date' => $endOfWeek->toDateString(),
            'total_income' => (float)$income,
            'total_expense' => (float)$expense,
            'balance' => (float)($income - $expense)
        ]);
    }

    public function monthly()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $income = Income::whereBetween('date', [$startOfMonth, $endOfMonth])->sum('amount');
        $expense = Expense::whereBetween('date', [$startOfMonth, $endOfMonth])->sum('amount');
        
        return response()->json([
            'report_type' => 'monthly',
            'month' => Carbon::now()->format('F Y'),
            'total_income' => (float)$income,
            'total_expense' => (float)$expense,
            'balance' => (float)($income - $expense)
        ]);
    }
}
