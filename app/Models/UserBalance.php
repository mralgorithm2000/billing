<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'balance'
    ];

    /**
     * Get the user associated with the balance.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
