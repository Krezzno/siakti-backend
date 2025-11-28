<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BudgetPlan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BudgetPlanController extends Controller
{
    /**
     * Tampilkan semua rencana anggaran pengguna.
     */
    public function index(Request $request)
    {
        $user = $request->user(); // Asumsi menggunakan Sanctum/Passport

        $budgetPlans = BudgetPlan::where('user_id', $user->id)
                                 ->orderBy('year', 'desc')
                                 ->orderBy('month', 'desc')
                                 ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $budgetPlans
        ]);
    }

    /**
     * Buat rencana anggaran baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'needs_budget' => 'required|numeric|min:0',
            'wants_budget' => 'required|numeric|min:0',
            'savings_budget' => 'required|numeric|min:0',
        ]);

        $user = $request->user();

        // Cek apakah sudah ada budget plan untuk bulan & tahun tersebut
        $existing = BudgetPlan::where('user_id', $user->id)
                              ->where('month', $validated['month'])
                              ->where('year', $validated['year'])
                              ->first();

        if ($existing) {
            throw ValidationException::withMessages([
                'month' => 'Rencana anggaran untuk bulan dan tahun ini sudah ada.'
            ]);
        }

        $budgetPlan = BudgetPlan::create([
            'user_id' => $user->id,
            'month' => $validated['month'],
            'year' => $validated['year'],
            'needs_budget' => $validated['needs_budget'],
            'wants_budget' => $validated['wants_budget'],
            'savings_budget' => $validated['savings_budget'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rencana anggaran berhasil dibuat.',
            'data' => $budgetPlan
        ], 201);
    }

    /**
     * Tampilkan rencana anggaran berdasarkan ID.
     */
    public function show($id)
    {
        $budgetPlan = BudgetPlan::findOrFail($id);

        // Validasi akses: hanya pemilik yang bisa lihat
        if ($budgetPlan->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke data ini.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $budgetPlan
        ]);
    }

    /**
     * Update rencana anggaran.
     */
    public function update(Request $request, $id)
    {
        $budgetPlan = BudgetPlan::findOrFail($id);

        if ($budgetPlan->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke data ini.'
            ], 403);
        }

        $validated = $request->validate([
            'month' => 'sometimes|required|integer|min:1|max:12',
            'year' => 'sometimes|required|integer|min:2000|max:2100',
            'needs_budget' => 'sometimes|required|numeric|min:0',
            'wants_budget' => 'sometimes|required|numeric|min:0',
            'savings_budget' => 'sometimes|required|numeric|min:0',
        ]);

        $budgetPlan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Rencana anggaran berhasil diperbarui.',
            'data' => $budgetPlan
        ]);
    }

    /**
     * Hapus rencana anggaran.
     */
    public function destroy($id)
    {
        $budgetPlan = BudgetPlan::findOrFail($id);

        if ($budgetPlan->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke data ini.'
            ], 403);
        }

        $budgetPlan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rencana anggaran berhasil dihapus.'
        ]);
    }
}