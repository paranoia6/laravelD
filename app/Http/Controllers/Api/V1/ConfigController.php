<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function getConfigs()
    {
        $configs = Config::select('config')->where('is_active', 1)->get();

        return response()->json([
            'message' => "successful",
            'data'    => [
                'configs'      => $configs
            ]
        ]);
    }
}
