<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
    ];

    protected $casts = [
        'type' => 'string',
    ];

    // Relasi
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scope untuk filter cepat
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    // Relasi ke expenses (opsional, untuk eager load)
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    // Relasi ke incomes? → Tidak langsung, karena incomes pakai `income_source`.
    // Tapi income_sources *bisa* punya relasi ke category jika nanti di-extend.
}