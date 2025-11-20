<?php

namespace Database\Seeders;

use App\Models\UserReport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserReportSeeder extends Seeder
{
    public function run()
    {
        $coords = [
            ['lat' => 3.2490, 'lng' => 101.7340],
            ['lat' => 3.2485, 'lng' => 101.7352],
        ];

        $severities = ['low', 'moderate', 'high', 'severe'];

        foreach ($coords as $i => $coord) {
            UserReport::create([
                'location' => "Reported Area " . ($i+1),
                'description' => "Flood reported by user at location " . ($i+1),
                'severity' => $severities[array_rand($severities)],
                'latitude' => $coord['lat'],
                'longitude' => $coord['lng'],
                'user_id' => 1,
            ]);
        }
    }
    /** public function run(): void
    *{
    *    //
    *} */
}
