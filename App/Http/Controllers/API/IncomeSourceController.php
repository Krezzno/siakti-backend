<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IncomeSource;
use Illuminate\Http\Request;

class IncomeSourceController extends Controller
{
    public function index(Request $request)
    {
        $sources = IncomeSource::where('user_id', $request->user()->id)
                               ->orderBy('name')
                               ->get(['id', 'name']);

        return response()->json(['success' => true, 'data' => $sources]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:income_sources,name,NULL,id,user_id,' . $request->user()->id
        ]);

        $source = IncomeSource::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sumber penghasilan berhasil ditambahkan.',
            'data' => $source
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $source = IncomeSource::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:income_sources,name,' . $id . ',id,user_id,' . $request->user()->id
        ]);

        $source->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Sumber penghasilan berhasil diperbarui.',
            'data' => $source
        ]);
    }

    public function destroy($id)
    {
        $source = IncomeSource::where('user_id', auth()->id())->findOrFail($id);

        // Cegah hapus jika masih dipakai di incomes
        if ($source->incomes()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa menghapus sumber penghasilan yang masih digunakan.'
            ], 422);
        }

        $source->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sumber penghasilan berhasil dihapus.'
        ]);
    }
}