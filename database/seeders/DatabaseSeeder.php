<?php

namespace Database\Seeders;

use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use App\Enums\UserRole;
use App\Models\User;

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

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Administrator',
                'password' => 'password',
                'role' => UserRole::Admin,
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'Maintenance Staff',
                'password' => 'password',
                'role' => UserRole::Staff,
            ]
        );
    }
}