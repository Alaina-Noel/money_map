<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paycheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pay_date',
        'amount',
    ];

    /**
     * Get the user that owns the paycheck.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
