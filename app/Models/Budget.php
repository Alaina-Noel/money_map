<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

/**
 * @property int                       $id
 * @property int                       $user_id
 * @property \Carbon\Carbon            $budget_month
 * @property string|null               $notes
 * @property float                     $total_expected_income
 * @property float                     $total_actual_income
 * @property float                     $total_expected_spending
 * @property float                     $total_actual_spending
 * @property float                     $remaining_budget
 * @property-read User                 $user
 * @property-read Collection<Category> $relatedCategories
 * @property-read Collection<Paycheck> $paychecks
 */

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'budget_month',
        'notes'
    ];

    protected $casts = [
        'budget_month' => 'datetime',
    ];

    protected $appends = [
        'total_expected_income',
        'total_actual_income',
        'total_expected_spending',
        'total_actual_spending',
        'remaining_budget'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function relatedCategories(): HasMany
    {
        return $this->hasMany(Category::class, 'budget_month', 'budget_month')
            ->where('user_id', $this->user_id);
    }

    public function paychecks(): HasMany
    {
        return $this->hasMany(Paycheck::class)
            ->whereMonth('pay_date', Carbon::parse($this->budget_month)->month)
            ->whereYear('pay_date', Carbon::parse($this->budget_month)->year);
    }

    // Computed Properties
    public function getTotalExpectedIncomeAttribute(): float
    {
        return $this->paychecks->sum('amount') ?? 0.0;
    }

    public function getTotalActualIncomeAttribute(): float
    {
        return $this->paychecks
            ->where('pay_date', '<=', now())
            ->sum('amount') ?? 0.0;
    }

    public function getTotalExpectedSpendingAttribute(): float
    {
        return $this->categories->sum('expected_amount') ?? 0.0;
    }

    public function getTotalActualSpendingAttribute(): float
    {
        return $this->categories->sum('actual_amount') ?? 0.0;
    }

    public function getRemainingBudgetAttribute(): float
    {
        return $this->total_expected_income - $this->total_actual_spending;
    }
}
