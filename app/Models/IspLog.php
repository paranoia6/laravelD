<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IspLog extends Model
{
    protected $table = 'isp_logs';

    protected $fillable = [
        'ip',
        'asn',
        'as_name',
    ];

}
