<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportLink extends Model
{
    protected $fillable = [
        'support_id',
        'title',
        'link',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function support(): BelongsTo
    {
        return $this->belongsTo(
            Support::class,
            'support_id'
        );
    }
}
