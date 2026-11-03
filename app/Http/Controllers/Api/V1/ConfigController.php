<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Config;
use App\Models\Log;
use App\Models\Support;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfigController extends Controller
{
    public function getConfigs()
    {
        $token = request()->bearerToken();

           Log::create(['meta_data' => $token]);

        $isActive = 0;
        $user = Auth::user();
        $accountType = $user->account_type;

        $support = Support::find($user->supporter_id);

        $configs = Config::select('config', 'internet_type', 'account_type')->where('is_active', 1)->get();

        if ($user->status == Account::STATUS_ACTIVE) {
            $isActive = 1;
        }

        if ($user->is_test == true) {
            $accountType = 3;
        }
        $rem = remainingDays($user->expired_at);

        if ($rem === '0') {
            $configs = [[
                'config' => 'vless://4ef77440-954b-45b6-bc91-59e5b09af5b5@172.67.193.3:8880?encryption=none&security=none&type=ws&host=bool.zanjiram.ir&path=%2F#%D8%A7%D8%B9%D8%AA%D8%A8%D8%A7%D8%B1%20%D8%AD%D8%B3%D8%A7%D8%A8%20%D8%B4%D9%85%D8%A7%20%D8%A8%D9%87%20%D8%A7%D8%AA%D9%85%D8%A7%D9%85%20%D8%B1%D8%B3%DB%8C%D8%AF%D9%87%20%D8%A7%D8%B3%D8%AA%20%D8%A8%D9%87%20%D9%BE%D8%B4%D8%AA%DB%8C%D8%A8%D8%A7%D9%86%DB%8C%20%D9%BE%DB%8C%D8%A7%D9%85%20%D8%AF%D9%87%DB%8C%D8%AF',
                'internet_type' => 3,
                'account_type' => 1
            ]];
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
