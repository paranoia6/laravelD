<?php

function getExpire($day)
{
    $date = new DateTime(now());

    $interval = new DateInterval('P'.$day.'D');
    $date->add($interval);

    return $date->format("Y-m-d H:i:s");
}

function checkedExpireAt($expiredAt){
    return date('Y-m-d H:i:s') >= $expiredAt;
}

function convertToPersianTypeAccount($account_type) {

    if ($account_type == 1){
        return "عادی";
    } else if($account_type == 2) {
        return "ویژه";
    } else {
        return "تست";
    }
}

function remainingDays(string $date): string
{
    $now = now();
    $expiresAt = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date);

    if ($expiresAt->isPast()) {
        return 'اتمام رسیده';
    }

    $days = $now->diffInDays($expiresAt);

    return '+'.floor($days);
}

if (! function_exists('jalali_date')) {
    function jalali_date($date, string $format = 'Y/m/d'): string
    {
        if ($date === null || $date === '') {
            return '';
        }

        try {
            return \Morilog\Jalali\Jalalian::fromDateTime($date)->format($format);
        } catch (\Throwable $e) {
            return '';
        }
    }
}

function encode($url){

// داده‌ای که به API ارسال می‌کنیم
    $data = json_encode([
        "url" => $url
    ]);

// شروع cURL
    $ch = curl_init("https://crypto.happ.su/api-v2.php");

// تنظیمات cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

// اجرا و گرفتن پاسخ
    $response = curl_exec($ch);

// بررسی خطا
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }

// بستن cURL
    curl_close($ch);

    $data = json_decode($response, true);

    $encryptedLink = $data['encrypted_link'] ?? null;

    return $encryptedLink;

}

function getEstimateExpired($client)
{
    $now = time(); // or your date as well
    $your_date = strtotime($client->expired_at);
    $datediff = $your_date - $now;

    return intval(round($datediff / (60 * 60 * 24)));
}
