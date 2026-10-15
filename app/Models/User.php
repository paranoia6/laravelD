<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'email',
        'role',
        'balance',
        'email_verified_at',
        'password',
        'is_active',
        'admin_blocked_at',
        'admin_block_reason',
        'expired_type',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'integer',
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'admin_blocked_at' => 'datetime',
            'deleted_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function createdWalletTransactions(): HasMany
    {
        return $this->hasMany(
            WalletTransaction::class,
            'created_by'
        );
    }

public function createdAccounts()
{
    return $this->hasMany(\App\Models\Account::class, 'admin_id');
}


    /**
     * Return the user's role as a string, whether role is an enum or a string.
     */
    public function roleValue(): ?string
    {
        $role = $this->role;

        if ($role instanceof \BackedEnum) {
            return (string) $role->value;
        }

        return is_string($role) ? $role : null;
    }

}
