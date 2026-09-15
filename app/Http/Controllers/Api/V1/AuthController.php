<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\Account;
use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\CalendarUtils;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | New Account Login
        |--------------------------------------------------------------------------
        */

        $account = Account::query()
            ->with(['plan', 'support'])
            ->where('username', $data['username'])
            ->first();

        if ($account) {

            /*
            |--------------------------------------------------------------------------
            | Blocked
            |--------------------------------------------------------------------------
            */

            if ($account->status === Account::STATUS_BLOCKED) {
                return response()->json([
                    'message' =>
                        'حساب شما مسدود می باشد.',
                    'data' => new \stdClass(),
                ], 403);
            }

            /*
            |--------------------------------------------------------------------------
            | Password
            |--------------------------------------------------------------------------
            |
            | Password در Account با encrypted ذخیره شده.
            |
            */

            if (
                ! hash_equals(
                    (string) $account->password,
                    (string) $data['password']
                )
            ) {
                return response()->json([
                    'message' =>
                        'نام کاربری یا کلمه عبور صحیح نمی باشد',
                    'data' => new \stdClass(),
                ], 401);
            }

            /*
            |--------------------------------------------------------------------------
            | First Login
            |--------------------------------------------------------------------------
            |
            | اعتبار اکانت فقط همین‌جا شروع می‌شود.
            |
            */

            if ($account->first_login_at === null) {

                DB::transaction(function () use ($account) {

                    $account->refresh();

                    if ($account->first_login_at === null) {

                        $now = now();

                        $account->first_login_at = $now;

                        $account->expired_at =
                            $now->copy()->addMonths(
                                (int) $account->plan->duration_months
                            );

                        $account->save();
                    }
                });

                $account->refresh();
            }

            /*
            |--------------------------------------------------------------------------
            | Expiration
            |--------------------------------------------------------------------------
            */

            if (
                $account->expired_at !== null
                &&
                $account->expired_at->isPast()
            ) {
                return response()->json([
                    'message' =>
                        'اعتبار حساب شما به اتمام رسیده جهت تمدید به پشتیبانی پیام دهید.',
                    'data' => new \stdClass(),
                ], 403);
            }

            /*
            |--------------------------------------------------------------------------
            | Token
            |--------------------------------------------------------------------------
            */

            $token = $account
                ->createToken('auth_token')
                ->plainTextToken;

            $firstLoginDate = null;

            if ($account->first_login_at) {
                $firstLoginDate =
                    CalendarUtils::strftime(
                        'H:i:s d-m-Y',
                        $account->first_login_at
                    );
            }

            return response()->json([
                'message' => 'successful',

                'data' => [
                    'token' => $token,

                    'token_type' => 'Bearer',

                    'user' => [
                        'id' =>
                            $account->id,

                        'username' =>
                            $account->username,

                        'first_login_date' =>
                            $firstLoginDate,

                        'first_login_at' =>
                            $account->first_login_at,

                        'expired_at' =>
                            $account->expired_at,

                        'is_active' =>
                            $account->status === Account::STATUS_ACTIVE,
                    ],
                ],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Legacy User Login
        |--------------------------------------------------------------------------
        |
        | برای Userهای قدیمی که username آنها email است.
        |
        */

        $user = User::query()
            ->select(
                'id',
                'email',
                'expired_type',
                'expired_at',
                'first_login_date',
                'account_type',
                'password',
                'is_active',
                'android_id',
                'supporter_id',
                'is_other_device_allow',
                'is_test'
            )
            ->where('email', $data['username'])
            ->where('device_type', 1)
            ->first();

        if (
            ! $user
            ||
            ! Hash::check(
                $data['password'],
                $user->password
            )
        ) {
            return response()->json([
                'message' =>
                    'نام کاربری یا کلمه عبور صحیح نمی باشد',
                'data' => new \stdClass(),
            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | Legacy User Activation
        |--------------------------------------------------------------------------
        */

        if ($user->expired_at === null) {

            $days = 31;

            switch ((int) $user->expired_type) {
                case 2:
                    $days = 61;
                    break;

                case 3:
                    $days = 91;
                    break;
            }

            if ($user->is_test) {
                $days = 1;
            }

            $expiredAt = getExpire($days);

            $user = $this->setPrimaryInformation(
                $expiredAt,
                $user->id,
                $data['android_id'] ?? null,
                $data['os_version'] ?? null,
                $data['device_model'] ?? null,
                $data['manufacturer'] ?? null
            );
        } else {

            if (! $user->is_active) {
                return response()->json([
                    'message' =>
                        'حساب شما به علت نقض قوانین مسدود می باشد',
                    'data' => new \stdClass(),
                ], 403);
            }

            if (checkedExpireAt($user->expired_at)) {
                return response()->json([
                    'message' =>
                        'اعتبار حساب شما به اتمام رسیده جهت تمدید به پشتیبانی پیام دهید.',
                    'data' => new \stdClass(),
                ], 403);
            }

            if (
                $user->is_other_device_allow == false
                &&
                $user->android_id != ($data['android_id'] ?? null)
            ) {
                return response()->json([
                    'message' =>
                        'حساب شما در دستگاه دیگری فعال شده در صورت تکرار حساب بلاک می شود.',
                    'data' => new \stdClass(),
                ], 403);
            }
        }

        $firstLoginDate = null;

        if ($user->first_login_date) {
            $firstLoginDate =
                CalendarUtils::strftime(
                    'H:i:s d-m-Y',
                    $user->first_login_date
                );
        }

        return response()->json([
            'message' => 'successful',

            'data' => [
                'token' =>
                    $user->createToken('auth_token')->plainTextToken,

                'token_type' =>
                    'Bearer',

                'user' => [
                    'id' =>
                        $user->id,

                    'email' =>
                        $user->email,

                    'first_login_date' =>
                        $firstLoginDate,

                    'is_active' =>
                        $user->is_active,
                ],
            ],
        ]);
    }

    public function setPrimaryInformation(
        $expiredAt,
        $userId,
        $androidId,
        $osVersion,
        $deviceModel,
        $manufacturer
    ) {
        $user = User::find($userId);

        $user->expired_at = $expiredAt;
        $user->first_login_date =
            now();

        $user->android_id =
            $androidId;

        $user->os_version =
            $osVersion;

        $user->device_model =
            $deviceModel;

        $user->manufacturer =
            $manufacturer;

        $user->save();

        return $user;
    }

    public function logout(Request $request)
    {
        $token = $request
            ->user()
            ->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return response()->json([
            'message' =>
                'Logout successful',

            'data' =>
                'Logout is completed',
        ]);
    }
}
