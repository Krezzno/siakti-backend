<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class IncomeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $incomes = $request->user()
            ->incomes()
            ->with(['incomeSource', 'category'])
            ->latest()
            ->get();
        return response()->json($incomes);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'income_source_id' => 'required|exists:income_sources,id',
            'category_id' => 'nullable|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'nullable|date',
            'description' => 'nullable|string|max:1000',
        ]);

        $request->user()->incomeSources()->findOrFail($data['income_source_id']);
        if (isset($data['category_id'])) {
            $request->user()->categories()->findOrFail($data['category_id']);
        }

        $income = $request->user()->incomes()->create($data);
        $income->load(['incomeSource', 'category']);
        return response()->json($income, 201);
    }

    public function show(Income $income): JsonResponse
    {
        $this->authorize('view', $income);
        $income->load(['incomeSource', 'category']);
        return response()->json($income);
    }

    public function update(Request $request, Income $income): JsonResponse
    {
        $this->authorize('update', $income);
        $data = $request->validate([
            'income_source_id' => 'sometimes|required|exists:income_sources,id',
            'category_id' => 'nullable|exists:categories,id',
            'amount' => 'sometimes|required|numeric|min:0.01',
            'date' => 'nullable|date',
            'description' => 'nullable|string|max:1000',
        ]);

        if (isset($data['income_source_id'])) {
            $request->user()->incomeSources()->findOrFail($data['income_source_id']);
        }
        if (isset($data['category_id'])) {
            $request->user()->categories()->findOrFail($data['category_id']);
        }

        $income->update($data);
        $income->load(['incomeSource', 'category']);
        return response()->json($income);
    }

    public function destroy(Income $income): JsonResponse
    {
        $this->authorize('delete', $income);
        $income->delete();
        return response()->json(null, 204);
    }
}