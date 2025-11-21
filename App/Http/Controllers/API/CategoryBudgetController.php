<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CategoryBudget;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryBudgetController extends Controller
{
    public function store(Request $request, string $budgetId): JsonResponse
    {
        $budget = $request->user()->budgets()->findOrFail($budgetId);
        $this->authorize('update', $budget);

        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'allocated_amount' => 'required|numeric|min:0',
        ]);

        $request->user()->categories()->findOrFail($data['category_id']);

        if ($budget->categoryBudgets()->where('category_id', $data['category_id'])->exists()) {
            return response()->json([
                'message' => 'Category already allocated to this budget.'
            ], 422);
        }

        $allocation = $budget->categoryBudgets()->create($data);
        $allocation->load('category');
        return response()->json($allocation, 201);
    }

    public function update(Request $request, CategoryBudget $categoryBudget): JsonResponse
    {
        $this->authorize('update', $categoryBudget->budget);
        $data = $request->validate([
            'allocated_amount' => 'required|numeric|min:0',
        ]);
        $categoryBudget->update($data);
        $categoryBudget->load('category');
        return response()->json($categoryBudget);
    }

    public function destroy(CategoryBudget $categoryBudget): JsonResponse
    {
        $this->authorize('update', $categoryBudget->budget);
        $categoryBudget->delete();
        return response()->json(null, 204);
    }
}