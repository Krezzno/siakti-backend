<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $categories = $request->user()->categories()->with('type')->get();
        return response()->json($categories);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'category_type_id' => 'required|exists:category_types,id',
            'category_name' => 'required|string|max:255',
        ]);

        $category = $request->user()->categories()->create($data);
        $category->load('type');
        return response()->json($category, 201);
    }

    public function show(Category $category): JsonResponse
    {
        $this->authorize('view', $category);
        $category->load('type');
        return response()->json($category);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $this->authorize('update', $category);
        $data = $request->validate([
            'category_type_id' => 'sometimes|required|exists:category_types,id',
            'category_name' => 'sometimes|required|string|max:255',
        ]);
        $category->update($data);
        $category->load('type');
        return response()->json($category);
    }

    public function destroy(Category $category): JsonResponse
    {
        $this->authorize('delete', $category);
        $category->delete();
        return response()->json(null, 204);
    }
}