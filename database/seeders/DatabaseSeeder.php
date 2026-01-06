<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'is_admin' => true, // 👈 Add this line
            'password' => bcrypt('password'), // optional: ensure you can log in
        ]);

        // Create a regular (non-admin) user
        User::factory()->create([
            'name' => 'Ali',
            'email' => 'user@example.com',
            'is_admin' => false,
            'password' => bcrypt('password'), // 👈 Same password for testing
        ]);

        $this->call([
            SensorDataSeeder::class,
            UserReportSeeder::class,
            SensorReadingsSeeder::class,
            UserReportsSeeder::class,
        ]);
    }
}
