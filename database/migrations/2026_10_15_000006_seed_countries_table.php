<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $countries = [
            ['name' => 'ایران', 'code' => 'IR', 'flag' => '🇮🇷'],
            ['name' => 'آلمان', 'code' => 'DE', 'flag' => '🇩🇪'],
            ['name' => 'ترکیه', 'code' => 'TR', 'flag' => '🇹🇷'],
            ['name' => 'هلند', 'code' => 'NL', 'flag' => '🇳🇱'],
            ['name' => 'آمریکا', 'code' => 'US', 'flag' => '🇺🇸'],
            ['name' => 'بریتانیا', 'code' => 'GB', 'flag' => '🇬🇧'],
            ['name' => 'فرانسه', 'code' => 'FR', 'flag' => '🇫🇷'],
            ['name' => 'کانادا', 'code' => 'CA', 'flag' => '🇨🇦'],
            ['name' => 'امارات', 'code' => 'AE', 'flag' => '🇦🇪'],
            ['name' => 'فنلاند', 'code' => 'FI', 'flag' => '🇫🇮'],
            ['name' => 'روسیه', 'code' => 'RU', 'flag' => '🇷🇺'],
            ['name' => 'سوئد', 'code' => 'SE', 'flag' => '🇸🇪'],
            ['name' => 'سوئیس', 'code' => 'CH', 'flag' => '🇨🇭'],
            ['name' => 'ایتالیا', 'code' => 'IT', 'flag' => '🇮🇹'],
            ['name' => 'اسپانیا', 'code' => 'ES', 'flag' => '🇪🇸'],
            ['name' => 'لهستان', 'code' => 'PL', 'flag' => '🇵🇱'],
            ['name' => 'سنگاپور', 'code' => 'SG', 'flag' => '🇸🇬'],
            ['name' => 'ژاپن', 'code' => 'JP', 'flag' => '🇯🇵'],
            ['name' => 'کره جنوبی', 'code' => 'KR', 'flag' => '🇰🇷'],
            ['name' => 'هند', 'code' => 'IN', 'flag' => '🇮🇳'],
            ['name' => 'برزیل', 'code' => 'BR', 'flag' => '🇧🇷'],
            ['name' => 'استرالیا', 'code' => 'AU', 'flag' => '🇦🇺'],
        ];

        foreach ($countries as &$country) {
            $country['created_at'] = $now;
            $country['updated_at'] = $now;
        }
        unset($country);

        DB::table('countries')->insertOrIgnore($countries);
    }

    public function down(): void
    {
        // رکوردهای کشور را حذف نمی‌کنیم تا اطلاعات موجود از بین نرود.
    }
};
