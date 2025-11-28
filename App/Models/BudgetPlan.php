<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'month',
        'year',
        'needs_budget',
        'wants_budget',
        'savings_budget'
    ];

    protected $casts = [
        'needs_budget' => 'decimal:2',
        'wants_budget' => 'decimal:2',
        'savings_budget' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Optional: Scope untuk filter berdasarkan user dan bulan/tahun
    public function scopeForUserAndMonth($query, $userId, $month, $year)
    {
        return $query->where('user_id', $userId)
                     ->where('month', $month)
                     ->where('year', $year);
    }
}