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
        return "سوپر ویژه";
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
