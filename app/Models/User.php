<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    protected $fillable = [
        'email',
        'role',
        'balance',
        'is_test',
        'email_verified_at',
        'pass',
        'password',
        'device_type',
        'expired_type',
        'expired_at',
        'first_login_date',
        'is_active',
        'admin_blocked_at',
        'admin_block_reason',
        'suspend_at',
        'account_type',
        'supporter_id',
        'device_model',
        'android_id',
        'os_version',
        'is_other_device_allow',
        'try_login',
        'last_seen',
        'manufacturer',
        'app_version_code',
        'seller_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'integer',
            'is_test' => 'boolean',
            'email_verified_at' => 'datetime',
            'device_type' => 'integer',
            'expired_type' => 'integer',
            'expired_at' => 'datetime',
            'first_login_date' => 'datetime',
            'is_active' => 'boolean',
            'admin_blocked_at' => 'datetime',
            'suspend_at' => 'datetime',
            'account_type' => 'integer',
            'supporter_id' => 'integer',
            'is_other_device_allow' => 'boolean',
            'try_login' => 'integer',
            'last_seen' => 'datetime',
            'seller_id' => 'integer',
            'password' => 'hashed',
            'deleted_at' => 'datetime',
        ];
    }
}
