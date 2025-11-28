<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoalContribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'financial_goal_id',
        'user_id',
        'amount',
        'note',
        'contribution_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'contribution_date' => 'date',
    ];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(FinancialGoal::class, 'financial_goal_id');
    }

    public function contributor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}