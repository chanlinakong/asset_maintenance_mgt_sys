<?php

namespace Database\Seeders;

use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $vehicles = Vehicle::factory()
            ->count(15)
            ->create();

        $vehicles->each(function (Vehicle $vehicle) {
            MaintenanceRecord::factory()
                ->count(fake()->numberBetween(1, 5))
                ->for($vehicle)
                ->create();
        });
    }
}