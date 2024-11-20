<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_expected_income',
        'total_actual_income',
        'total_expected_spending',
        'total_actual_spending',
        'leftover',
    ];

    /**
     * Get the user that owns the budget summary.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
