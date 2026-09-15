<?php

function getExpire($day)
{
    $date = new DateTime(now());

    $interval = new DateInterval('P'.$day.'D');
    $date->add($interval);

    return $date->format("Y-m-d H:i:s");
}

function checkedExpireAt($expiredAt)
{
    return date('Y-m-d H:i:s') >= $expiredAt;
}

function convertToPersianTypeAccount($account_type)
{
    if ($account_type == 1) {
        return "عادی";
    } elseif ($account_type == 2) {
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


/*
|--------------------------------------------------------------------------
| Jalali Date / Tehran Time
|--------------------------------------------------------------------------
*/

if (! function_exists('jalali_date')) {
    function jalali_date(
        mixed $date,
        string $format = 'Y/m/d H:i'
    ): string {
        if ($date === null || $date === '') {
            return '—';
        }

        try {
            if ($date instanceof \Carbon\CarbonInterface) {
                $carbon = $date->copy()->setTimezone('Asia/Tehran');
            } else {
                $carbon = \Carbon\Carbon::parse($date, 'Asia/Tehran');
            }

            return \Morilog\Jalali\Jalalian::fromCarbon($carbon)
                ->format($format);

        } catch (\Throwable) {
            return '—';
        }
    }
}
