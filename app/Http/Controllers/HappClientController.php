<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Client;
use App\Models\Config;
use App\Models\Log;
use App\Models\Server;
use App\Models\Support;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class HappClientController extends Controller
{
    function checkRequest(Request $request) {
        $client = Account::where('username', '=', $request->t)->firstOrFail();

        $record = Support::find($client->supporter_id);

        $links = $record->meta_data['links'] ?? [];

        $url = !empty($links['telegram'])
            ? $links['telegram']
            : 'https://t.me/بدون_پشتیبان';

        $agent = $request->header('user-agent');


        $expire = now()->addDays(30)->timestamp;

        if (str_contains(strtolower($agent), 'happ')) {
            header("Content-Type: text/plain");
            header("Content-Disposition: attachment; filename=\"sub\"");
            header("support-url: " . $url);
            header("subscription-userinfo: expire=". $expire);
            header("profile-title: خوش آمدید");
            header("profile-update-interval: 2");
            header("announce: برای پینگ  و آپدیت خودکار هنگام ورود تنظیمات مربوطه را انجام دهید در غیر اینصورت هر ۲ ساعت یکبار بروزرسانی برای شما انجام می شود");
        } else {
            header("subscription-userinfo: expire=". $expire);
        }
        if ($client->is_active == 0) {
            echo "vless://be8ce0cf-8e72-4695-b216-a1800d125213@104.22.61.34:8080?security=&type=ws&path=/zad&host=js.rtuser.ir#حساب کاربری شما به اتمام رسیده جهت تمدید به پشتیبانی پیام دهید". "\n";
            return;
        }

    //    Log::create(['meta_data' => $request]);
  //      Log::create(['meta_data' => $request->t]);

        //   if (str_contains($agent, 'happ')) {
        $result = $this->checkClient($client, $request);

        if ($result) {
            $configs = $this->getConfigs();
            $day = getEstimateExpired($client);
            if ($client->expired_at && Carbon::parse($client->expired_at)->isPast()) {
                echo "vless://be8ce0cf-8e72-4695-b216-a1800d125213@104.22.61.34:8080?security=&type=ws&path=/zad&host=js.rtuser.ir#  اعتبار شما به اتمام رسیده جهت تمدید به پشتیبانی پیام دهید" ."\n";
                // تاریخ گذشته است
            } else {
                foreach ($configs as $config) {

                    echo $config->config . "\n";
                }
            }
        } else {
            echo "vless://be8ce0cf-8e72-4695-b216-a1800d125213@104.22.61.34:8080?security=&type=ws&path=/zad&host=js.rtuser.ir#  اکانت  در دستگاه دیگری فعال است " ."\n";
        }

        if (str_contains($url, 'rubika.ir')) {
            $username = Str::after($url, 'https://rubika.ir/');
            $title = 'آیدی پشتیبانی روبیکا';
        } elseif (str_contains($url, 't.me')) {
            $username = Str::after($url, 'https://t.me/');
            $title =  'آیدی تلگرام';
        } else {
            $username = '';
            $title = 'پشتیبانی';
        }

        echo "vless://b772cd07-f06b-47bd-a613-d8b47e0a6d16@mngvc.dfed.com:2095?security=none&encryption=none&type=ws&host=de.rtuser.ir&path=/#{$title} {$username}\n";
    }

    private function checkClient($client, $request) {
        $deviceModel = strtolower($request->header('X-Device-Model'));
     //   $deviceOs = $request->header('x-Device-Os');
        $hwid = $request->header('X-Hwid');
        $verOs = $request->header('X-Ver-Os');

     //   $deviceId = $this->getOsId($deviceOs);

        if (is_null($client->device_model)){
            // new user
            if ($client->is_test) {
                $days = 2;
            } else {
                $days = $client->expired_type * 30;
            }

            $client->device_model = $deviceModel;
            $client->expired_at = getExpire($days);
            $client->hwid = $hwid;
            $client->os_version = $verOs;
            $client->first_login_date = date('Y-m-d H:i:s');
            $client->last_seen = date('Y-m-d H:i:s');

            $client->save();

            return true;
        } elseif ($client->device_model == $deviceModel) {
            $client->last_seen = date('Y-m-d H:i:s');
            $client->save();

            return true;
        }

        return false;
    }

    public function getConfigs()
    {
        return Config::where('is_active', 1)->get();
    }


    public function isUser1($client, $cookieName): bool
    {
        return $client->cookie == $_COOKIE[$cookieName];
    }

    private function checkCookie($client, $cookieName): bool
    {
        if (isset($_COOKIE[$cookieName])) {
            $key = $_COOKIE[$cookieName];
            return $client->cookie == $key || $client->cookie2 == $key;
        } else {
            return false;
        }
    }

    public function getCookie($cookieName){
        return $_COOKIE[$cookieName];
    }

    public function setCookie($cookieName, $value, $time){
        setcookie($cookieName, $value, time() + ($time), "/"); // 86400 = 1 day
        return true;
    }


    private function getOsId($os){
        if (strtolower($os) == "android") {
            return 1;
        } else {
            return 2;
        }
    }
}
