<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int                       $id
 * @property int                       $user_id
 * @property string                    $name
 * @property float                     $expected_amount
 * @property float                     $actual_amount
 * @property Carbon                    $budget_month
 * @property float                     $spending_percentage
 * @property float                     $remaining_amount
 * @property-read User                 $user
 * @property-read Collection<LineItem> $lineItems
 */
class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'expected_amount',
        'actual_amount',
        'budget_month',
    ];

    protected $casts = [
        'budget_month' => 'date',
        'expected_amount' => 'float',
        'actual_amount' => 'float'
    ];

    protected $appends = ['spending_percentage', 'remaining_amount'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lineItems(): HasMany
    {
        return $this->hasMany(LineItem::class);
    }

    public function getSpendingPercentageAttribute(): float
    {
        return $this->expected_amount > 0
            ? ($this->actual_amount / $this->expected_amount) * 100
            : 0;
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->expected_amount - $this->actual_amount;
    }
}
