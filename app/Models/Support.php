<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /**
     * شبکه‌های پشتیبانی قابل استفاده
     */
    public static function networks(): array
    {
        return [
            'bale' => 'بله',
            'eitaa' => 'ایتا',
            'rubika' => 'روبیکا',
            'telegram' => 'تلگرام',
            'whatsapp' => 'واتساپ',
            'instagram' => 'اینستاگرام',
        ];
    }

    /**
     * لینک‌های ذخیره‌شده این Support
     *
     * ساختار:
     *
     * [
     *     'bale' => '',
     *     'eitaa' => '',
     *     'rubika' => '',
     *     'telegram' => 'https://t.me/...',
     *     'whatsapp' => '',
     *     'instagram' => '',
     * ]
     */
    public function getLinksAttribute(): array
    {
        $metaData = $this->meta_data;

        if (! is_array($metaData)) {
            return [];
        }

        $links = $metaData['links'] ?? [];

        return is_array($links)
            ? $links
            : [];
    }

    /**
     * دریافت لینک یک شبکه
     */
    public function getNetworkLink(
        string $network
    ): ?string {
        $link = $this->links[$network] ?? null;

        if (
            ! is_string($link)
            || trim($link) === ''
        ) {
            return null;
        }

        return trim($link);
    }

    /**
     * آیا حداقل یک شبکه برای Support ثبت شده؟
     */
    public function hasLinks(): bool
    {
        foreach (
            self::networks()
            as $network => $label
        ) {
            if (
                $this->getNetworkLink($network)
                !== null
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * آیا یک شبکه مشخص لینک دارد؟
     */
    public function hasNetwork(
        string $network
    ): bool {
        return $this->getNetworkLink($network)
            !== null;
    }

    /**
     * عنوان فارسی شبکه
     */
    public static function networkLabel(
        string $network
    ): string {
        return self::networks()[$network]
            ?? $network;
    }

    /**
     * ذخیره لینک‌های شبکه‌ها در meta_data
     */
    public function setLinks(
        array $links
    ): void {
        $normalized = [];

        foreach (
            self::networks()
            as $network => $label
        ) {
            $value = $links[$network] ?? '';

            $normalized[$network] =
                is_string($value)
                    ? trim($value)
                    : '';
        }

        $metaData = is_array($this->meta_data)
            ? $this->meta_data
            : [];

        $metaData['links'] = $normalized;

        $this->meta_data = $metaData;
    }

    /**
     * ساخت Support جدید با ساختار استاندارد لینک‌ها
     */
    public static function createWithLinks(
        int $userId,
        string $name,
        array $links = []
    ): self {
        $support = new self();

        $support->user_id = $userId;
        $support->name = trim($name);
        $support->is_active = true;

        $support->setLinks($links);

        $support->save();

        return $support;
    }

    /**
     * تبدیل مقدار واردشده به URL نهایی
     */
    public static function buildLink(
        string $network,
        string $value
    ): string {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        if (
            str_starts_with(
                $value,
                'http://'
            )
            ||
            str_starts_with(
                $value,
                'https://'
            )
        ) {
            return $value;
        }

        return match ($network) {
            'whatsapp' =>
                'https://wa.me/'
                . ltrim($value, '+'),

            'instagram' =>
                'https://instagram.com/'
                . ltrim($value, '@'),

            'telegram' =>
                'https://t.me/'
                . ltrim($value, '@'),

            'rubika' =>
                'https://rubika.ir/'
                . ltrim($value, '@'),

            'eitaa' =>
                'https://eitaa.com/'
                . ltrim($value, '@'),

            'bale' =>
                'https://ble.ir/'
                . ltrim($value, '@'),

            default =>
            $value,
        };
    }

    /**
     * بررسی مقدار ورودی یک شبکه
     */
    public static function validateNetworkValue(
        string $network,
        string $value
    ): bool {
        $value = trim($value);

        if ($value === '') {
            return true;
        }

        if ($network === 'whatsapp') {
            return (bool) preg_match(
                '/^[0-9]{8,15}$/',
                $value
            );
        }

        return (bool) preg_match(
            '/^[A-Za-z0-9_.@\/:-]+$/',
            $value
        );
    }
}
