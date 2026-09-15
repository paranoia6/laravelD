<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            ['type' => 'normal', 'duration_months' => 1],
            ['type' => 'normal', 'duration_months' => 2],
            ['type' => 'normal', 'duration_months' => 3],
            ['type' => 'special', 'duration_months' => 1],
            ['type' => 'special', 'duration_months' => 2],
            ['type' => 'special', 'duration_months' => 3],
        ];

        foreach ($plans as $plan) {
            Plan::firstOrCreate(
                [
                    'type' => $plan['type'],
                    'duration_months' => $plan['duration_months'],
                ],
                [
                    'price' => null,
                    'is_active' => true,
                ]
            );
        }
    }
}
