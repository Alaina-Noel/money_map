<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int           $id
 * @property int           $user_id
 * @property int           $category_id
 * @property string        $description
 * @property float         $amount
 * @property Carbon        $date
 * @property string|null   $notes
 * @property-read Carbon   $budget_month
 * @property-read User     $user
 * @property-read Category $category
 */
class LineItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'description',
        'amount',
        'date',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'float'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getBudgetMonthAttribute()
    {
        return $this->category->budget_month;
    }
}
