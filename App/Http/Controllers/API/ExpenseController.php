<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ExpenseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $expenses = $request->user()
            ->expenses()
            ->with(['category', 'budget'])
            ->latest()
            ->get();
        return response()->json($expenses);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'budget_id' => 'nullable|exists:budgets,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'nullable|date',
            'description' => 'nullable|string|max:1000',
        ]);

        $request->user()->categories()->findOrFail($data['category_id']);
        if (isset($data['budget_id'])) {
            $request->user()->budgets()->findOrFail($data['budget_id']);
        }

        $expense = $request->user()->expenses()->create($data);
        $expense->load(['category', 'budget']);
        return response()->json($expense, 201);
    }

    public function show(Expense $expense): JsonResponse
    {
        $this->authorize('view', $expense);
        $expense->load(['category', 'budget']);
        return response()->json($expense);
    }

    public function update(Request $request, Expense $expense): JsonResponse
    {
        $this->authorize('update', $expense);
        $data = $request->validate([
            'category_id' => 'sometimes|required|exists:categories,id',
            'budget_id' => 'nullable|exists:budgets,id',
            'amount' => 'sometimes|required|numeric|min:0.01',
            'date' => 'nullable|date',
            'description' => 'nullable|string|max:1000',
        ]);

        if (isset($data['category_id'])) {
            $request->user()->categories()->findOrFail($data['category_id']);
        }
        if (isset($data['budget_id'])) {
            $request->user()->budgets()->findOrFail($data['budget_id']);
        }

        $expense->update($data);
        $expense->load(['category', 'budget']);
        return response()->json($expense);
    }

    public function destroy(Expense $expense): JsonResponse
    {
        $this->authorize('delete', $expense);
        $expense->delete();
        return response()->json(null, 204);
    }
}