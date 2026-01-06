<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserReportsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $severities = ['ankle', 'knee','waist', 'chest', 'head'];
        $locations = [
            'IIUM Gombak',
            'Mahallah Aminah',
            'KICT Building',
            'Masjid Sultan Ahmad Shah',
            'River Side Area'
        ];

        $ranges = [
            30,     // 1 month
            90,     // 3 months
            180,    // 6 months
            365,    // 1 year
            730,    // 2 years
            1095,   // 3 years
            1460,   // 4 years
            1825    // 5 years
        ];

        foreach ($ranges as $days) {
            for ($i = 0; $i < rand(20, 40); $i++) {
                DB::table('user_reports')->insert([
                    'user_id'     => rand(3, 4),
                    'location'    => $locations[array_rand($locations)],
                    'description' => 'Flood reported due to heavy rainfall.',
                    'severity'    => $severities[array_rand($severities)],
                    'latitude'    => 3.252 + rand(-10, 10) / 1000,
                    'longitude'   => 101.734 + rand(-10, 10) / 1000,
                    'image'       => null,
                    'status'      => 'cleared',
                    'created_at'  => Carbon::now()->subDays(rand(1, $days)),
                    'updated_at'  => Carbon::now()->subDays(rand(1, $days)),
                ]);
            }
        }

    }
}
