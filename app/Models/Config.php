<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $fillable = [
        'config',
        'internet_type',
        'account_type',
        'is_active',
        'descriptions',
    ];

    protected function casts(): array
    {
        return [
            'internet_type' => 'integer',
            'account_type' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
