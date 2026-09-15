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
    public function getConfigs(Request $request)
    {
        $authenticated = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | New Account authentication
        |--------------------------------------------------------------------------
        */

        if ($authenticated instanceof Account) {
            $account = $authenticated->loadMissing([
                'plan',
                'support.links',
            ]);

            $accountType = $account->plan?->type === 'special'
                ? 2
                : 1;

            /*
             * Account is already tied to a support owned by its creator.
             * Do not trust arbitrary support IDs from the request.
             */
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

            /*
             * Preserve the old config response shape.
             */
            $configs = Config::query()
                ->select('config')
                ->where('is_active', true)
                ->where('account_type', '<=', $accountType)
                ->get();

            /*
             * Before first login the account has no expiry yet.
             * After first login, show remaining days.
             */
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

        /*
        |--------------------------------------------------------------------------
        | Legacy User authentication
        |--------------------------------------------------------------------------
        */

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
                ->select('config')
                ->where('is_active', true)
                ->where('account_type', '<=', $accountType)
                ->get();

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

        /*
        |--------------------------------------------------------------------------
        | Unknown authenticated model
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'message' => 'حساب کاربری معتبر نیست.',
            'data' => new \stdClass(),
        ], 401);
    }
}
