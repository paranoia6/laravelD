<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'balance_after',
        'description',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'balance_after' => 'integer',
            'user_id' => 'integer',
            'created_by' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
