<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

class Account extends Authenticatable
{
    use HasApiTokens;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_BLOCKED = 'blocked';

    protected $fillable = [
        'admin_id',
        'plan_id',
        'supporter_id',
        'device_type',
        'username',
        'password',
        'charged_amount',
        'activated_at',
        'first_login_at',
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
            'charged_amount' => 'integer',
            'device_type' => 'integer',
            'activated_at' => 'datetime',
            'first_login_at' => 'datetime',
            'expired_at' => 'datetime',
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
