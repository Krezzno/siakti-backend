<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialGoal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'target_amount',
        'target_date',
        'status',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'target_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(GoalContribution::class);
    }

    // Accessor: progress dalam persen
    public function getProgressPercentageAttribute()
    {
        $total = $this->contributions()->sum('amount');
        return $this->target_amount > 0 ? min(100, ($total / $this->target_amount) * 100) : 0;
    }

    // Accessor: total terkumpul
    public function getTotalCollectedAttribute()
    {
        return $this->contributions()->sum('amount');
    }
}