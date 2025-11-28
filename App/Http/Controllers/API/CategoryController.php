<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    /**
     * GET /api/categories
     * Filter: ?type=expense | ?type=income | (default: semua)
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Category::where('user_id', $user->id);

        // Filter by type
        if ($request->filled('type')) {
            $type = $request->type;
            if (!in_array($type, ['income', 'expense'])) {
                throw ValidationException::withMessages([
                    'type' => 'Type hanya boleh "income" atau "expense".'
                ]);
            }
            $query->where('type', $type);
        }

        $categories = $query->orderBy('name')->get(['id', 'name', 'type']);

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * POST /api/categories
     * Required: name, type
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,NULL,id,user_id,' . $request->user()->id . ',type,' . $request->type,
            'type' => 'required|in:income,expense',
        ]);

        $category = Category::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'type' => $validated['type'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan.',
            'data' => $category
        ], 201);
    }

    /**
     * GET /api/categories/{id}
     */
    public function show($id)
    {
        $category = Category::where('user_id', auth()->id())
                            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $category]);
    }

    /**
     * PUT/PATCH /api/categories/{id}
     */
    public function update(Request $request, $id)
    {
        $category = Category::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100|unique:categories,name,' . $id . ',id,user_id,' . $request->user()->id . ',type,' . $request->input('type', $category->type),
            'type' => 'sometimes|required|in:income,expense',
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui.',
            'data' => $category
        ]);
    }

    /**
     * DELETE /api/categories/{id}
     */
    public function destroy($id)
    {
        $category = Category::where('user_id', auth()->id())->findOrFail($id);

        // Cegah hapus jika masih dipakai di expenses (karena hanya expense pakai langsung)
        if ($category->type === 'expense' && $category->expenses()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa menghapus kategori pengeluaran yang masih digunakan.'
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus.'
        ]);
    }

    /**
     * Opsional: GET /api/categories/options
     * Untuk dropdown di frontend (lebih ringan, tanpa pagination)
     */
    public function options(Request $request)
    {
        $type = $request->query('type', 'expense'); // default: expense

        $categories = Category::where('user_id', $request->user()->id)
                              ->where('type', $type)
                              ->orderBy('name')
                              ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}