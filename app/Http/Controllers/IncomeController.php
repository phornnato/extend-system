<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index()
    {
        return Income::with('category')->latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'description' => 'required|string',
            'date' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        return Income::create($validated);
    }

    public function show(Income $income)
    {
        return $income->load('category');
    }

    public function update(Request $request, Income $income)
    {
        $validated = $request->validate([
            'amount' => 'sometimes|numeric',
            'description' => 'sometimes|string',
            'date' => 'sometimes|date',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $income->update($validated);
        return $income;
    }

    public function destroy(Income $income)
    {
        $income->delete();
        return response()->noContent();
    }
}
