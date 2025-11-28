<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Expense::with(['category:id,name', 'paymentMethod:id,name,type'])
                        ->where('user_id', $user->id)
                        ->orderBy('date', 'desc')
                        ->orderBy('created_at', 'desc');

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $expenses = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $expenses
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date|before_or_equal:today',
            'note' => 'nullable|string|max:500',
        ]);

        // Pastikan category & payment method milik user
        $user = $request->user();
        $category = \App\Models\Category::where('id', $validated['category_id'])
                                        ->where('user_id', $user->id)
                                        ->firstOrFail();
        $method = \App\Models\PaymentMethod::where('id', $validated['payment_method_id'])
                                           ->where('user_id', $user->id)
                                           ->firstOrFail();

        $expense = Expense::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'payment_method_id' => $method->id,
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'note' => $validated['note'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil ditambahkan.',
            'data' => $expense->load(['category:id,name', 'paymentMethod:id,name,type'])
        ], 201);
    }

    public function show($id)
    {
        $expense = Expense::with(['category:id,name', 'paymentMethod:id,name,type'])
                          ->where('user_id', auth()->id())
                          ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $expense]);
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'sometimes|required|exists:categories,id',
            'payment_method_id' => 'sometimes|required|exists:payment_methods,id',
            'amount' => 'sometimes|required|numeric|min:0.01',
            'date' => 'sometimes|required|date|before_or_equal:today',
            'note' => 'nullable|string|max:500',
        ]);

        if ($request->filled('category_id')) {
            $category = \App\Models\Category::where('id', $validated['category_id'])
                                            ->where('user_id', auth()->id())
                                            ->firstOrFail();
            $validated['category_id'] = $category->id;
        }

        if ($request->filled('payment_method_id')) {
            $method = \App\Models\PaymentMethod::where('id', $validated['payment_method_id'])
                                               ->where('user_id', auth()->id())
                                               ->firstOrFail();
            $validated['payment_method_id'] = $method->id;
        }

        $expense->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil diperbarui.',
            'data' => $expense->fresh()->load(['category:id,name', 'paymentMethod:id,name,type'])
        ]);
    }

    public function destroy($id)
    {
        $expense = Expense::where('user_id', auth()->id())->findOrFail($id);
        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil dihapus.'
        ]);
    }
}