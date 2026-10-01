<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Config;
use App\Models\Support;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfigController extends Controller
{
    public function getConfigs()
    {
        $user = Auth::user();
        $accountType = $user->account_type;

        $support = Support::find($user->supporter_id);

        $configs = Config::query()
            ->with('country:id,name,code,flag')
            ->select('id', 'config', 'country_id', 'internet_type', 'account_type')
            ->where('is_active', 1)
            ->get()
            ->map(fn (Config $config) => [
                'id' => $config->id,
                'config' => $config->config,
                'country' => $config->country ? [
                    'name' => $config->country->name,
                    'code' => $config->country->code,
                    'flag' => $config->country->flag,
                ] : null,
                'country_name' => $config->country?->name,
                'country_flag' => $config->country?->flag,
                'internet_type' => $config->internet_type,
                'account_type' => $config->account_type,
            ]);

        return response()->json([
            'message' => 'successful',
            'data' => [
                'configs' => $configs,
                'supports' => json_decode($support->meta_data),
                'user' => [
                    'days' => remainingDays($user->expired_at) . ' روز ',
                    'account_type_translate' => convertToPersianTypeAccount($accountType),
                    'account_type' => $accountType,
                    'device_model' => $user->device_model,
                    'is_active' => $user->is_active,
                ],
            ],
        ]);
    }

    public function getAccountConfigs(Request $request)
    {
        $authenticated = Auth::user();

        if ($authenticated instanceof Account) {
            $account = $authenticated->loadMissing([
                'plan',
            ]);

            $accountType = $account->plan?->type === 'special'
                ? 2
                : 1;

            $support = $account->support;

            if (
                $support
                && (
                    ! $support->is_active
                    || (int) $support->user_id !== (int) $account->admin_id
                )
            ) {
                $support = null;
            }

            $configs = Config::query()
                ->with('country:id,name,code,flag')
                ->select('id', 'config', 'country_id')
                ->where('is_active', true)
                ->where('account_type', '<=', $accountType)
                ->get()
                ->map(fn (Config $config) => [
                    'id' => $config->id,
                    'config' => $config->config,
                    'country' => $config->country ? [
                        'name' => $config->country->name,
                        'code' => $config->country->code,
                        'flag' => $config->country->flag,
                    ] : null,
                    'country_name' => $config->country?->name,
                    'country_flag' => $config->country?->flag,
                ]);

            $days = $account->expired_at
                ? remainingDays($account->expired_at) . ' روز '
                : 'فعال نشده';

            $accountTypeLabel = function_exists('convertToPersianTypeAccount')
                ? convertToPersianTypeAccount($accountType)
                : ($accountType === 2 ? 'ویژه' : 'عادی');

            return response()->json([
                'message' => 'successful',
                'data' => [
                    'configs' => $configs,
                    'supports' => $support?->meta_data ?? [],
                    'user' => [
                        'id' => $account->id,
                        'username' => $account->username,
                        'email' => $account->username,
                        'days' => $days,
                        'account_type' => $accountTypeLabel,
                        'device_model' => null,
                    ],
                ],
            ]);
        }

        if ($authenticated instanceof User) {
            $user = $authenticated;

            $accountType = (int) ($user->account_type ?? 1);

            $support = null;

            if ($user->supporter_id) {
                $support = Support::query()
                    ->whereKey($user->supporter_id)
                    ->where('user_id', $user->id)
                    ->where('is_active', true)
                    ->first();
            }

            $configs = Config::query()
                ->with('country:id,name,code,flag')
                ->select('id', 'config', 'country_id')
                ->where('is_active', true)
                ->where('account_type', '<=', $accountType)
                ->get()
                ->map(fn (Config $config) => [
                    'id' => $config->id,
                    'config' => $config->config,
                    'country' => $config->country ? [
                        'name' => $config->country->name,
                        'code' => $config->country->code,
                        'flag' => $config->country->flag,
                    ] : null,
                    'country_name' => $config->country?->name,
                    'country_flag' => $config->country?->flag,
                ]);

            $accountTypeLabel = function_exists('convertToPersianTypeAccount')
                ? convertToPersianTypeAccount($accountType)
                : ($accountType === 2 ? 'ویژه' : 'عادی');

            return response()->json([
                'message' => 'successful',
                'data' => [
                    'configs' => $configs,
                    'supports' => $support?->meta_data ?? [],
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'days' => remainingDays($user->expired_at) . ' روز ',
                        'account_type' => $accountTypeLabel,
                        'device_model' => $user->device_model,
                    ],
                ],
            ]);
        }

        return response()->json([
            'message' => 'حساب کاربری معتبر نیست.',
            'data' => new \stdClass(),
        ], 401);
    }
}
