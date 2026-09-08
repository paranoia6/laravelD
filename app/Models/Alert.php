<?php

namespace App\Models;

use App\Enums\AlertType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $fillable = [
        'type',
        'title',
        'message',
        'button_text',
        'button_url',
        'platform',
        'min_build_number',
        'max_build_number',
        'priority',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => AlertType::class,
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function scopeAvailableFor(
        Builder $query,
        int $buildNumber
    ): Builder {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $query) use ($buildNumber) {
                $query
                    ->whereNull('max_build_number')
                    ->orWhere('max_build_number', '>=', $buildNumber);
            })
            ->where(function (Builder $query) {
                $query
                    ->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $query) {
                $query
                    ->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            });
    }
}
