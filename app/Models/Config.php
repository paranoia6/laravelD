<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Config extends Model
{
    protected $fillable = [
        'config',
        'country_id',
        'internet_type',
        'account_type',
        'is_active',
        'descriptions',
    ];

    protected function casts(): array
    {
        return [
            'country_id' => 'integer',
            'internet_type' => 'integer',
            'account_type' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
