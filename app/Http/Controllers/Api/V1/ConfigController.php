<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Config;
use App\Models\Support;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfigController extends Controller
{
    public function getConfigs()
    {
        $user = Auth::user();
        $accountType = $user->account_type;

        $support = Support::find($user->supporter_id);

        $configs = Config::select('config', 'internet_type', 'account_type')->where('is_active', 1)->get();

        return response()->json([
            'message' => "successful",
            'data'    => [
                'configs'      => $configs,
                'supports' => json_decode($support->meta_data),
                'user' => [
                    'days' => remainingDays($user->expired_at). ' روز ',
                    'account_type_translate' => convertToPersianTypeAccount($accountType),
                    'account_type' => $accountType,
                    'device_model' => $user->device_model,
                    'is_active' => $user->is_active
                ]
            ]
        ]);
    }
}
