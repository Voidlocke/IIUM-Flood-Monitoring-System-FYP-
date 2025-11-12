<?php

namespace Database\Seeders;
use App\Models\SensorData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SensorDataSeeder extends Seeder
{
    public function run()
    {
        $coords = [
            ['lat' => 3.2500, 'lng' => 101.7345],
            ['lat' => 3.2515, 'lng' => 101.7350],
            ['lat' => 3.2488, 'lng' => 101.7332],
        ];

        foreach ($coords as $i => $coord) {
            SensorData::create([
                'location' => "Sensor Location " . ($i+1),
                'water_level' => rand(0, 100), // cm
                'latitude' => $coord['lat'],
                'longitude' => $coord['lng'],
            ]);
        }
    }

    /** public function run(): void
     *{
    *
    *} */
}
