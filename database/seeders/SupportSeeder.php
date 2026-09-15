<?php

namespace Database\Seeders;

use App\Models\Support;
use Illuminate\Database\Seeder;

class SupportSeeder extends Seeder
{
    public function run(): void
    {
        $networks = [
            'whatsapp',
            'instagram',
            'telegram',
            'rubika',
            'eitaa',
            'bale',
        ];

        foreach ($networks as $network) {
            Support::firstOrCreate(
                ['name' => $network],
                [
                    'meta_data' => [],
                ]
            );
        }
    }
}
