<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Config;
use App\Models\Support;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfigController extends Controller
{
    public function getConfigs()
    {
        $isActive = false;
        $user = Auth::user();
        $accountType = $user->account_type;

        $support = Support::find($user->supporter_id);

        $configs = Config::select('config', 'internet_type', 'account_type')->where('is_active', 1)->get();

        if ($user->status == Account::STATUS_ACTIVE) {
            $isActive = true;
        }

        return response()->json([
            'message' => "successful",
            'data'    => [
                'configs'      => $configs,
                'supports' => $support->meta_data,
                'user' => [
                    'days' => remainingDays($user->expired_at). ' روز ',
                    'account_type_translate' => convertToPersianTypeAccount($accountType),
                    'account_type' => $accountType,
                    'device_model' => $user->device_model,
                    'is_active' => $isActive
                ]
            ]
        ]);
    }
}
