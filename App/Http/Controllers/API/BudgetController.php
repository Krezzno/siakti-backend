<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BudgetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $budgets = $request->user()
            ->budgets()
            ->with('categoryBudgets.category')
            ->get();
        return response()->json($budgets);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'budget_name' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0',
            'start_date' => 'nullable|date|before_or_equal:end_date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $budget = $request->user()->budgets()->create($data);
        $budget->load('categoryBudgets.category');
        return response()->json($budget, 201);
    }

    public function show(Budget $budget): JsonResponse
    {
        $this->authorize('view', $budget);
        $budget->load('categoryBudgets.category');
        return response()->json($budget);
    }

    public function update(Request $request, Budget $budget): JsonResponse
    {
        $this->authorize('update', $budget);
        $data = $request->validate([
            'budget_name' => 'sometimes|required|string|max:255',
            'total_amount' => 'sometimes|required|numeric|min:0',
            'start_date' => 'nullable|date|before_or_equal:end_date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        $budget->update($data);
        $budget->load('categoryBudgets.category');
        return response()->json($budget);
    }

    public function destroy(Budget $budget): JsonResponse
    {
        $this->authorize('delete', $budget);
        $budget->delete();
        return response()->json(null, 204);
    }
}