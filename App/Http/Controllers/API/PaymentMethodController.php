<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $methods = PaymentMethod::where('user_id', $user->id)
                                ->orderBy('is_default', 'desc')
                                ->orderBy('created_at', 'desc')
                                ->get();

        return response()->json(['success' => true, 'data' => $methods]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:cash,bank,e-wallet',
            'account_number' => 'nullable|string|max:50',
            'bank_name' => 'required_if:type,bank|nullable|string|max:100',
            'is_default' => 'boolean'
        ]);

        $user = $request->user();

        // Jika ini di-set sebagai default, unset default sebelumnya
        if ($request->boolean('is_default')) {
            PaymentMethod::where('user_id', $user->id)
                         ->update(['is_default' => false]);
        }

        $method = PaymentMethod::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'type' => $validated['type'],
            'account_number' => $validated['account_number'] ?? null,
            'bank_name' => $validated['bank_name'] ?? null,
            'is_default' => $request->boolean('is_default', false),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Metode pembayaran berhasil ditambahkan.',
            'data' => $method
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $method = PaymentMethod::findOrFail($id);

        if ($method->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'type' => 'sometimes|required|in:cash,bank,e-wallet',
            'account_number' => 'nullable|string|max:50',
            'bank_name' => 'required_if:type,bank|nullable|string|max:100',
            'is_default' => 'boolean',
        ]);

        // Handle toggle is_default
        if ($request->has('is_default') && $request->boolean('is_default')) {
            PaymentMethod::where('user_id', $request->user()->id)
                         ->where('id', '!=', $id)
                         ->update(['is_default' => false]);
            $validated['is_default'] = true;
        }

        $method->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Metode pembayaran diperbarui.',
            'data' => $method
        ]);
    }

    public function destroy($id)
    {
        $method = PaymentMethod::findOrFail($id);

        if ($method->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        // Cegah hapus jika ini satu-satunya default & user masih punya metode lain
        if ($method->is_default && PaymentMethod::where('user_id', $method->user_id)->count() > 1) {
            // Pindahkan default ke metode lain (misal: paling baru)
            $fallback = PaymentMethod::where('user_id', $method->user_id)
                                     ->where('id', '!=', $id)
                                     ->latest()
                                     ->first();
            if ($fallback) $fallback->update(['is_default' => true]);
        }

        $method->delete();

        return response()->json([
            'success' => true,
            'message' => 'Metode pembayaran dihapus.'
        ]);
    }
}