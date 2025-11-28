<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Income;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Income::with(['incomeSource:id,name', 'paymentMethod:id,name,type'])
                       ->where('user_id', $user->id)
                       ->orderBy('date', 'desc')
                       ->orderBy('created_at', 'desc');

        // Filter opsional: date_from, date_to, source_id
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        if ($request->filled('source_id')) {
            $query->where('income_source_id', $request->source_id);
        }

        $incomes = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $incomes
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'income_source_id' => 'required|exists:income_sources,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date|before_or_equal:today',
            'description' => 'nullable|string|max:500',
            'is_regular' => 'boolean',
        ]);

        // Pastikan income_source & payment_method milik user yg sama
        $user = $request->user();
        $source = \App\Models\IncomeSource::where('id', $validated['income_source_id'])
                                          ->where('user_id', $user->id)
                                          ->firstOrFail();
        $method = \App\Models\PaymentMethod::where('id', $validated['payment_method_id'])
                                           ->where('user_id', $user->id)
                                           ->firstOrFail();

        $income = Income::create([
            'user_id' => $user->id,
            'income_source_id' => $source->id,
            'payment_method_id' => $method->id,
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'description' => $validated['description'] ?? null,
            'is_regular' => $request->boolean('is_regular', false),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Penghasilan berhasil ditambahkan.',
            'data' => $income->load(['incomeSource:id,name', 'paymentMethod:id,name,type'])
        ], 201);
    }

    public function show($id)
    {
        $income = Income::with(['incomeSource:id,name', 'paymentMethod:id,name,type'])
                        ->where('user_id', auth()->id())
                        ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $income]);
    }

    public function update(Request $request, $id)
    {
        $income = Income::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'income_source_id' => 'sometimes|required|exists:income_sources,id',
            'payment_method_id' => 'sometimes|required|exists:payment_methods,id',
            'amount' => 'sometimes|required|numeric|min:0.01',
            'date' => 'sometimes|required|date|before_or_equal:today',
            'description' => 'nullable|string|max:500',
            'is_regular' => 'boolean',
        ]);

        // Validasi kepemilikan relasi jika diubah
        if ($request->filled('income_source_id')) {
            $source = \App\Models\IncomeSource::where('id', $validated['income_source_id'])
                                              ->where('user_id', auth()->id())
                                              ->firstOrFail();
            $validated['income_source_id'] = $source->id;
        }

        if ($request->filled('payment_method_id')) {
            $method = \App\Models\PaymentMethod::where('id', $validated['payment_method_id'])
                                               ->where('user_id', auth()->id())
                                               ->firstOrFail();
            $validated['payment_method_id'] = $method->id;
        }

        $income->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Penghasilan berhasil diperbarui.',
            'data' => $income->fresh()->load(['incomeSource:id,name', 'paymentMethod:id,name,type'])
        ]);
    }

    public function destroy($id)
    {
        $income = Income::where('user_id', auth()->id())->findOrFail($id);
        $income->delete();

        return response()->json([
            'success' => true,
            'message' => 'Penghasilan berhasil dihapus.'
        ]);
    }
}