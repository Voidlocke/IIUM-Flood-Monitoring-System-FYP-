<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SensorData;
use App\Models\SensorReading;

class FluctuateSensors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sensors:fluctuate {--min=0} {--max=120}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Randomly fluctuates dummy sensor water levels (in cm)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $min = (float) $this->option('min'); // cm
        $max = (float) $this->option('max'); // cm

        // Only active sensors if you have is_active column
        $query = SensorData::query();
        if (\Schema::hasColumn('sensor_data', 'is_active')) {
            $query->where('is_active', true);
        }

        $sensors = $query->get();

        foreach ($sensors as $sensor) {
            $current = (float) $sensor->water_level; // cm

            // Small random step up/down (cm)
            // 80% small movement, 15% medium rise, 5% big spike
            $chance = random_int(1, 100);

            if ($chance <= 80) {
                $delta = random_int(-3, 4);
            } elseif ($chance <= 95) {
                $delta = random_int(5, 12);
            } else {
                $delta = random_int(15, 30); // sudden heavy rain
            }

            // Optional: add a little “wave” behavior (tends to drift back down)
            if ($current > 80) $delta -= random_int(2, 6);
            if ($current < 10) $delta += random_int(1, 4);

            $new = $current + $delta;

            // Clamp to bounds
            $new = max($min, min($max, $new));

            $sensor->water_level = $new;
            $sensor->save();

            // ✅ SAVE HISTORY (THIS IS THE KEY)
            SensorReading::create([
                'sensor_data_id' => $sensor->id,
                'water_level'    => $new,
            ]);
        }

        $this->info("Updated {$sensors->count()} sensors.");
        return self::SUCCESS;

    }
}
