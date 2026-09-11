<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\LoginRequest;
use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $data = $request->validated();

     //   Log::create(['meta_data' => $request->all()]);

        $email = $data['username'];

        // 2. Fetch User
        $user = User::select('id', 'email', 'expired_type', 'expired_at', 'first_login_date', 'account_type',
            'password', 'is_active', 'android_id', 'supporter_id', 'is_other_device_allow', 'is_test')
            ->where('email', $email)
            ->where('device_type', 1)
            ->first();

        // 3. Check Credentials
        if (!$user || !Hash::check($data['password'], $user->password)) {
            // Return empty body (represented as an empty object or omitted) with an appropriate error message
            return response()->json([
                'message' => 'نام کاربری یا کلمه عبور صحیح نمی باشد',
                'data'    => new \stdClass() // Empty body
            ], 401); // 401 Unauthorized is standard for bad credentials
        }

        // 4. Handle First Login vs Subsequent Logins
        if ($user->expired_at == null) {
            // First login logic
            $days = 31;

            switch ($user->expired_type) {
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

            $expired_at = getExpire($days);

            $user = $this->setPrimaryInformation($expired_at, $user->id, $data['android_id'], $data['os_version'], $data['device_model'], $data['manufacturer']);

        } else {
            // Subsequent login logic

            if (!$user->is_active) {
                return response()->json([
                    'message' => 'حساب شما به علت نقض قوانین مسدود می باشد',
                    'data'    => new \stdClass()
                ], 403); // 403 Forbidden
            }

            if (checkedExpireAt($user->expired_at)) {
                return response()->json([
                    'message' => 'اعتبار حساب شما به اتمام رسیده جهت تمدید به پشتیبانی پیام دهید.',
                    'data'    => new \stdClass()
                ], 403);
            }

            if ($user->is_other_device_allow == false && $user->android_id !=  $data['android_id']) {
                // Fraud attempt detected
      //          $userRepository->updateTryLogin($user->id);

                return response()->json([
                    'message' => 'حساب شما در دستگاه دیگری فعال شده در صورت تکرار حساب بلاک می شود.',
                    'data'    => new \stdClass()
                ], 403);
            }
        }

        $firstLoginDate = \Morilog\Jalali\CalendarUtils::strftime('H:i:s d-m-Y', $user->first_login_date);

        $userInformation = [
            'id'              => $user->id,
            'email'           => $user->email,
            'first_login_date'=> $firstLoginDate,
            'is_active'       => $user->is_active
        ];

        // 6. Return consistent, clean API structure for Android client
        return response()->json([
            'message' => "successful",
            'data'    => [
                'token'      => $user->createToken('auth_token')->plainTextToken,
                'token_type' => 'Bearer',
                'user'       => $userInformation,
            ]
        ]);
    }

    public function setPrimaryInformation($expired_at, $userId, $androidId, $osVersion, $deviceModel, $manufacturer)
    {
        $user = User::find($userId);

        $user->expired_at = $expired_at;
        $user->first_login_date = date('Y-m-d H:i:s', time());
        $user->android_id = $androidId;
        $user->os_version = $osVersion;
        $user->device_model = $deviceModel;
        $user->manufacturer = $manufacturer;

        $user->save();

        return $user;
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
            'data' => 'Logout is completed',
        ]);
    }
}
