<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinancialGoal;
use App\Models\GoalContribution;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GoalContributionController extends Controller
{
    /**
     * Tampilkan semua kontribusi untuk goal tertentu.
     * Hanya pemilik goal yang boleh lihat.
     */
    public function index(Request $request, $goalId)
    {
        $user = $request->user();

        $goal = FinancialGoal::where('id', $goalId)
                             ->where('user_id', $user->id)
                             ->firstOrFail();

        $contributions = GoalContribution::with('contributor:id,name,email')
                                         ->where('financial_goal_id', $goal->id)
                                         ->orderBy('contribution_date', 'desc')
                                         ->paginate(10);

        return response()->json([
            'success' => true,
            'goal' => [
                'id' => $goal->id,
                'title' => $goal->title,
                'target_amount' => $goal->target_amount,
                'total_collected' => $goal->total_collected,
                'progress_percentage' => $goal->progress_percentage,
            ],
            'data' => $contributions
        ]);
    }

    /**
     * Tambah kontribusi baru ke sebuah goal.
     * Hanya pemilik goal yang boleh menambah (bisa diubah jika izinkan kolaborator).
     */
    public function store(Request $request, $goalId)
    {
        $user = $request->user();

        // Pastikan goal ada & milik user
        $goal = FinancialGoal::where('id', $goalId)
                             ->where('user_id', $user->id)
                             ->firstOrFail();

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:500',
            'contribution_date' => 'nullable|date|before_or_equal:today',
        ]);

        // Cegah kontribusi jika goal sudah completed/canceled
        if ($goal->status !== 'active') {
            throw ValidationException::withMessages([
                'goal' => 'Tidak bisa menambah kontribusi ke goal yang tidak aktif.'
            ]);
        }

        $contribution = GoalContribution::create([
            'financial_goal_id' => $goal->id,
            'user_id' => $user->id, // contributor = pemilik goal (bisa diubah jadi $request->input('contributor_id', $user->id))
            'amount' => $validated['amount'],
            'note' => $validated['note'],
            'contribution_date' => $validated['contribution_date'] ?? now()->toDateString(),
        ]);

        // Opsional: update status goal ke 'completed' jika tercapai
        $total = $goal->total_collected + $contribution->amount;
        if ($total >= $goal->target_amount && $goal->status === 'active') {
            $goal->update(['status' => 'completed']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kontribusi berhasil ditambahkan.',
            'data' => $contribution->load('contributor:id,name,email')
        ], 201);
    }

    /**
     * Tampilkan detail kontribusi.
     */
    public function show($goalId, $id)
    {
        $user = auth()->user();

        $contribution = GoalContribution::with('goal', 'contributor:id,name,email')
                                        ->where('id', $id)
                                        ->whereHas('goal', fn($q) => $q->where('user_id', $user->id))
                                        ->firstOrFail();

        return response()->json(['success' => true, 'data' => $contribution]);
    }

    /**
     * Perbarui kontribusi (misal: edit amount atau note).
     */
    public function update(Request $request, $goalId, $id)
    {
        $user = auth()->user();

        $contribution = GoalContribution::where('id', $id)
                                        ->whereHas('goal', fn($q) => $q->where('user_id', $user->id))
                                        ->firstOrFail();

        $validated = $request->validate([
            'amount' => 'sometimes|required|numeric|min:0.01',
            'note' => 'nullable|string|max:500',
            'contribution_date' => 'nullable|date|before_or_equal:today',
        ]);

        $oldAmount = $contribution->amount;
        $contribution->update($validated);

        // Update status goal jika perlu (misal: setelah edit, goal jadi tercapai/belum)
        $goal = $contribution->goal;
        $total = $goal->contributions()->sum('amount');

        if ($goal->status === 'active' && $total >= $goal->target_amount) {
            $goal->update(['status' => 'completed']);
        } elseif ($goal->status === 'completed' && $total < $goal->target_amount) {
            $goal->update(['status' => 'active']); // rollback ke active
        }

        return response()->json([
            'success' => true,
            'message' => 'Kontribusi berhasil diperbarui.',
            'data' => $contribution->fresh()
        ]);
    }

    /**
     * Hapus kontribusi (soft delete).
     */
    public function destroy($goalId, $id)
    {
        $user = auth()->user();

        $contribution = GoalContribution::where('id', $id)
                                        ->whereHas('goal', fn($q) => $q->where('user_id', $user->id))
                                        ->firstOrFail();

        $contribution->delete(); // soft delete

        // Update status goal jika perlu
        $goal = $contribution->goal;
        $total = $goal->contributions()->sum('amount');

        if ($goal->status === 'completed' && $total < $goal->target_amount) {
            $goal->update(['status' => 'active']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kontribusi berhasil dihapus.'
        ]);
    }
}