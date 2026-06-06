<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        try {
            if (!$request->user()) {
                return response()->json(['message' => 'Unauthorized - no user', 'error' => 'Please login first'], 401);
            }
            return Income::where('user_id', $request->user()->id)
                ->with('category')
                ->latest()
                ->get();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Income index error', 'error' => $e->getMessage()], 500);
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

        return Income::create([
            'user_id' => $request->user()->id,
            ...$validated,
        ]);
    }

    public function show(Request $request, Income $income)
    {
        if ($income->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return $income->load('category');
    }

    public function update(Request $request, Income $income)
    {
        if ($income->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'amount' => 'sometimes|numeric',
            'description' => 'sometimes|string',
            'date' => 'sometimes|date',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $income->update($validated);
        return $income;
    }

    public function destroy(Request $request, Income $income)
    {
        if ($income->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $income->delete();
        return response()->noContent();
    }
}
