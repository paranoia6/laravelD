<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'name',
        'code',
        'flag',
    ];

    public function configs(): HasMany
    {
        return $this->hasMany(Config::class);
    }
}
