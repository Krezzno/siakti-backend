<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinancialGoal;
use App\Models\GoalContribution;
use Illuminate\Http\Request;

class FinancialGoalController extends Controller
{
    // ===== GOAL =====

    public function index(Request $request)
    {
        $goals = FinancialGoal::with('contributions')
                              ->where('user_id', $request->user()->id)
                              ->orderBy('target_date', 'asc')
                              ->get();

        return response()->json(['success' => true, 'data' => $goals]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:1',
            'target_date' => 'nullable|date|after_or_equal:today',
            'status' => 'in:active,completed,canceled'
        ]);

        $goal = FinancialGoal::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'target_amount' => $validated['target_amount'],
            'target_date' => $validated['target_date'] ?? null,
            'status' => $validated['status'] ?? 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tujuan keuangan berhasil dibuat.',
            'data' => $goal
        ], 201);
    }

    public function show($id)
    {
        $goal = FinancialGoal::with('contributions.contributor')
                             ->where('user_id', auth()->id())
                             ->findOrFail($id);
        return response()->json(['success' => true, 'data' => $goal]);
    }

    public function update(Request $request, $id)
    {
        $goal = FinancialGoal::where('user_id', auth()->id())->findOrFail($id);
        $validated = $request->validate([
            'title' => 'sometimes|string|max:150',
            'description' => 'nullable|string',
            'target_amount' => 'sometimes|numeric|min:1',
            'target_date' => 'nullable|date|after_or_equal:today',
            'status' => 'in:active,completed,canceled'
        ]);

        $goal->update($validated);
        return response()->json(['success' => true, 'data' => $goal->fresh()]);
    }

    public function destroy($id)
    {
        $goal = FinancialGoal::where('user_id', auth()->id())->findOrFail($id);
        $goal->delete(); // soft delete
        return response()->json(['success' => true, 'message' => 'Tujuan dihapus.']);
    }

    // ===== CONTRIBUTION =====

    public function addContribution(Request $request, $goalId)
    {
        $goal = FinancialGoal::where('user_id', auth()->id())->findOrFail($goalId);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string',
            'contribution_date' => 'nullable|date|before_or_equal:today',
        ]);

        $contribution = GoalContribution::create([
            'financial_goal_id' => $goal->id,
            'user_id' => auth()->id(), // contributor = pemilik (bisa diubah jadi allow guest/user lain)
            'amount' => $validated['amount'],
            'note' => $validated['note'],
            'contribution_date' => $validated['contribution_date'] ?? now()->toDateString(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kontribusi berhasil ditambahkan.',
            'data' => $contribution->load('contributor')
        ], 201);
    }

    public function getContributions($goalId)
    {
        $goal = FinancialGoal::where('user_id', auth()->id())->findOrFail($goalId);
        $contributions = GoalContribution::with('contributor')
                                         ->where('financial_goal_id', $goal->id)
                                         ->orderBy('contribution_date', 'desc')
                                         ->get();
        return response()->json(['success' => true, 'data' => $contributions]);
    }
}