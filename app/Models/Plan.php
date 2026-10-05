<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'type',
        'duration_months',
        'price',
        'is_active',
    ];


    protected function casts(): array
    {
        return [
            'duration_months' =>
                'integer',

            'price' =>
                'integer',

            'is_active' =>
                'boolean',
        ];
    }
}
