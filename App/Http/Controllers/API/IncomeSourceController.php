<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\IncomeSource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class IncomeSourceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json($request->user()->incomeSources);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'source_name' => 'required|string|max:255|unique:income_sources,source_name,NULL,id,user_id,' . $request->user()->id,
        ]);

        $source = $request->user()->incomeSources()->create($data);
        return response()->json($source, 201);
    }

    public function show(IncomeSource $incomeSource): JsonResponse
    {
        $this->authorize('view', $incomeSource);
        return response()->json($incomeSource);
    }

    public function update(Request $request, IncomeSource $incomeSource): JsonResponse
    {
        $this->authorize('update', $incomeSource);
        $data = $request->validate([
            'source_name' => 'sometimes|required|string|max:255|unique:income_sources,source_name,' . $incomeSource->id . ',id,user_id,' . $request->user()->id,
        ]);
        $incomeSource->update($data);
        return response()->json($incomeSource);
    }

    public function destroy(IncomeSource $incomeSource): JsonResponse
    {
        $this->authorize('delete', $incomeSource);
        $incomeSource->delete();
        return response()->json(null, 204);
    }
}