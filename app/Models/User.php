<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'email',
    'role',
    'balance',
    'name',
    'is_active',
    'pass',
    'password',
    'is_test',
    'device_type',
    'expired_type',
    'expired_at',
    'first_login_date',
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
    'admin_blocked_at',
    'admin_block_reason',
])]

#[Hidden([
    'password',
    'pass',
    'remember_token',
])]

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;


    protected function casts(): array
    {
        return [
            'email_verified_at' =>
                'datetime',

            'password' =>
                'hashed',

            'role' =>
                \App\Enums\UserRole::class,

            'is_test' =>
                'boolean',

            'expired_at' =>
                'datetime',

            'first_login_date' =>
                'datetime',

            'is_active' =>
                'boolean',

            'suspend_at' =>
                'datetime',

            'is_other_device_allow' =>
                'boolean',

            'last_seen' =>
                'datetime',

            'device_type' =>
                'integer',

            'expired_type' =>
                'integer',

            'account_type' =>
                'integer',

            'supporter_id' =>
                'integer',

            'try_login' =>
                'integer',

            'seller_id' =>
                'integer',

            'admin_blocked_at' =>
                'datetime',
        ];
    }


    /**
     * Support پیش‌فرض کاربر
     */
    public function support(): BelongsTo
    {
        return $this->belongsTo(
            Support::class,
            'supporter_id'
        );
    }


    /**
     * تمام Supportهایی که این کاربر ساخته
     */
    public function supports(): HasMany
    {
        return $this->hasMany(
            Support::class,
            'user_id'
        );
    }


    /**
     * تراکنش‌های Wallet
     */
    public function walletTransactions(): HasMany
    {
        return $this->hasMany(
            WalletTransaction::class
        );
    }


    /**
     * اکانت‌هایی که این Admin ساخته
     */
    public function createdAccounts(): HasMany
    {
        return $this->hasMany(
            Account::class,
            'admin_id'
        );
    }


    /**
     * تراکنش‌هایی که این کاربر ایجاد کرده
     */
    public function createdWalletTransactions(): HasMany
    {
        return $this->hasMany(
            WalletTransaction::class,
            'created_by'
        );
    }

}
