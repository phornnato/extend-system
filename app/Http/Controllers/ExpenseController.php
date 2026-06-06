<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        try {
            if (!$request->user()) {
                return response()->json(['message' => 'Unauthorized - no user', 'error' => 'Please login first'], 401);
            }
            return Expense::where('user_id', $request->user()->id)
                ->with('category')
                ->latest()
                ->get();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Expense index error', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'description' => 'required|string',
            'date' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        return Expense::create([
            'user_id' => $request->user()->id,
            ...$validated,
        ]);
    }

    public function show(Request $request, Expense $expense)
    {
        if ($expense->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return $expense->load('category');
    }

    public function update(Request $request, Expense $expense)
    {
        if ($expense->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'amount' => 'sometimes|numeric',
            'description' => 'sometimes|string',
            'date' => 'sometimes|date',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $expense->update($validated);
        return $expense;
    }

    public function destroy(Request $request, Expense $expense)
    {
        if ($expense->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $expense->delete();
        return response()->noContent();
    }
}
