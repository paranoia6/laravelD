<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Support extends Model
{
    protected $fillable = [
        'name',
        'user_id',
        'meta_data',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'meta_data' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function links(): HasMany
    {
        return $this->hasMany(
            SupportLink::class,
            'support_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /**
     * ساخت ۶ شبکه پیش‌فرض برای هر کاربر
     */
    public static function ensureDefaultsFor(
        User $user
    ): void {
        $networks = [
            'whatsapp' => 'واتساپ',
            'instagram' => 'اینستاگرام',
            'telegram' => 'تلگرام',
            'rubika' => 'روبیکا',
            'eitaa' => 'ایتا',
            'bale' => 'بله',
        ];

        foreach ($networks as $key => $label) {

            static::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'name' => $key,
                ],
                [
                    'meta_data' => [
                        'network' => $key,
                        'title' => $label,
                    ],
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * کلید شبکه
     */
    public function getNetworkKeyAttribute(): string
    {
        $name = strtolower(
            trim((string) $this->name)
        );

        return match ($name) {
            'whatsapp', 'واتساپ', 'واتس اپ' => 'whatsapp',
            'instagram', 'اینستاگرام' => 'instagram',
            'telegram', 'تلگرام' => 'telegram',
            'rubika', 'روبیکا' => 'rubika',
            'eitaa', 'ایتا' => 'eitaa',
            'bale', 'بله' => 'bale',
            default => $name,
        };
    }

    /**
     * عنوان فارسی شبکه
     */
    public function getNetworkLabelAttribute(): string
    {
        return match ($this->network_key) {
            'whatsapp' => 'واتساپ',
            'instagram' => 'اینستاگرام',
            'telegram' => 'تلگرام',
            'rubika' => 'روبیکا',
            'eitaa' => 'ایتا',
            'bale' => 'بله',
            default => $this->name,
        };
    }
}
