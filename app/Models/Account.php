<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Account extends Authenticatable
{
    use HasApiTokens;

    public const STATUS_ACTIVE = 1;

    public const STATUS_BLOCKED = 0;

    protected $fillable = [
        'admin_id',
        'plan_id',
        'device_type',
        'supporter_id',
        'username',
        'password',
        'charged_amount',
        'is_test',
        'first_login_date',
        'expired_at',
        'status',
        'blocked_at',
        'block_reason',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'admin_id' => 'integer',
            'plan_id' => 'integer',
            'device_type' => 'integer',
            'supporter_id' => 'integer',
            'charged_amount' => 'integer',
            'is_test' => 'boolean',
            'first_login_date' => 'datetime',
            'expired_at' => 'datetime',
            'status' => 'integer',
            'blocked_at' => 'datetime',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function support(): BelongsTo
    {
        return $this->belongsTo(Support::class, 'supporter_id');
    }
}
