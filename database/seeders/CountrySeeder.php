<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Symfony\Component\Intl\Countries;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        foreach (Countries::getNames('fa') as $code => $name) {
            $code = strtoupper((string) $code);

            if (!preg_match('/^[A-Z]{2}$/', $code) || $code === 'ZZ') {
                continue;
            }

            Country::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'flag' => $this->flag($code),
                ]
            );
        }
    }

    private function flag(string $code): string
    {
        return implode('', array_map(
            static fn (string $char): string => mb_chr(127397 + ord($char)),
            str_split($code)
        ));
    }
}
