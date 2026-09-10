<?php

namespace Database\Factories;

use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MaintenanceRecord>
 */
class MaintenanceRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $reportedAt = fake()->dateTimeBetween('-1 year', 'now');

        $startedAt = fake()->dateTimeBetween(
            $reportedAt,
            'now'
        );

        $completedAt = fake()->optional(0.7)->dateTimeBetween(
            $startedAt,
            'now'
        );

        $status = $completedAt
            ? MaintenanceStatus::Completed
            : fake()->randomElement([
                MaintenanceStatus::Pending,
                MaintenanceStatus::InProgress,
            ]);

        return [
            'vehicle_id' => Vehicle::factory(),

            'title' => fake()->randomElement([
                'Engine Oil Change',
                'Brake Inspection',
                'Engine Repair',
                'Tire Replacement',
                'Battery Replacement',
                'Hydraulic System Repair',
                'Transmission Service',
                'Routine Inspection',
                'Cooling System Repair',
            ]),

            'description' => fake()->sentence(),

            'type' => fake()->randomElement(
                MaintenanceType::cases()
            ),

            'status' => $status,

            'reported_at' => $reportedAt,

            'started_at' => $startedAt,

            'completed_at' => $completedAt,

            'cost' => fake()->randomFloat(
                2,
                20,
                5000
            ),

            'service_provider' => fake()->randomElement([
                'Internal Workshop',
                'ABC Auto Service',
                'Port Maintenance Team',
                'Hino Service Center',
                'Toyota Service Center',
            ]),

            'notes' => fake()->optional()->sentence(),
        ];
    }
}