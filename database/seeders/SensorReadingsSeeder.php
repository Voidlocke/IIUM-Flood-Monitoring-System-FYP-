<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SensorReadingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sensorIds = range(1, 4);

        $this->seedRange(
            start: Carbon::now()->subMonth(),
            step: '1 hour',
            sensorIds: $sensorIds
        );

        $this->seedRange(
            start: Carbon::now()->subMonths(3),
            step: '6 hours',
            sensorIds: $sensorIds
        );

        $this->seedRange(
            start: Carbon::now()->subMonths(6),
            step: '1 day',
            sensorIds: $sensorIds
        );

        $this->seedRange(
            start: Carbon::now()->subYear(),
            step: '3 days',
            sensorIds: $sensorIds
        );

        $this->seedRange(
            start: Carbon::now()->subYears(5),
            step: '1 week',
            sensorIds: $sensorIds
        );
    }

    private function seedRange(Carbon $start, string $step, array $sensorIds): void
    {
        $current = $start->copy();

        while ($current->lessThanOrEqualTo(now())) {
            foreach ($sensorIds as $sensorId) {
                DB::table('sensor_readings')->insert([
                    'sensor_data_id' => $sensorId,
                    'water_level'    => rand(50, 400) / 100, // 0.5m – 4.0m
                    'created_at'     => $current,
                    'updated_at'     => $current,
                ]);
            }

            $current->add($this->parseStep($step));
        }
    }

    private function parseStep(string $step): \DateInterval
    {
        return match ($step) {
            '1 hour'  => new \DateInterval('PT1H'),
            '6 hours' => new \DateInterval('PT6H'),
            '1 day'   => new \DateInterval('P1D'),
            '3 days'  => new \DateInterval('P3D'),
            '1 week'  => new \DateInterval('P7D'),
        };
    }
}
