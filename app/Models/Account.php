<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Account extends Authenticatable
{
    use HasApiTokens;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_BLOCKED = 'blocked';

    protected $fillable = [
        'admin_id',
        'plan_id',
        'device_type',
        'supporter_id',
        'username',
        'password',
        'expired_type',
        'account_type',
        'device_model',
        'android_id',
        'is_test',
        'os_version',
        'is_other_device_allow',
        'try_login',
        'last_seen',
        'manufacturer',
        'app_version_code',
        'charged_amount',
        'expired_at',
        'first_login_date',
        'is_active',
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
            'expired_type' => 'integer',
            'account_type' => 'integer',
            'is_test' => 'boolean',
            'is_other_device_allow' => 'boolean',
            'try_login' => 'integer',
            'charged_amount' => 'integer',
            'expired_at' => 'datetime',
            'first_login_date' => 'datetime',
            'last_seen' => 'datetime',
            'is_active' => 'boolean',
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
