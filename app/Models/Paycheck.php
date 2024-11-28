<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int         $id
 * @property int         $user_id
 * @property Carbon      $pay_date
 * @property float       $amount
 * @property string      $source
 * @property string|null $notes
 * @property-read User   $user
 */
class Paycheck extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'pay_date',
        'amount',
        'source',
        'notes'
    ];

    protected $casts = [
        'pay_date' => 'date',
        'amount' => 'float'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
