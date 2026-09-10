<?php

namespace Database\Factories;

use App\Enums\VehicleStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'vehicle_code' => fake()->unique()->bothify('VH-###'),

            'name' => fake()->randomElement([
                'Cargo Truck',
                'Forklift',
                'Container Truck',
                'Crane',
                'Loader',
                'Excavator',
                'Pickup Truck',
            ]),

            'type' => fake()->randomElement([
                'Truck',
                'Forklift',
                'Crane',
                'Loader',
                'Excavator',
                'Pickup',
            ]),

            'brand' => fake()->randomElement([
                'Hino',
                'Toyota',
                'Isuzu',
                'Komatsu',
                'Caterpillar',
                'Mitsubishi',
            ]),

            'model' => fake()->bothify('Model-###'),

            'registration_number' => fake()->unique()->bothify('2A-####'),

            'status' => fake()->randomElement(VehicleStatus::cases()),

            'purchase_date' => fake()
                ->dateTimeBetween('-10 years', '-1 month')
                ->format('Y-m-d'),

            'notes' => fake()->optional()->sentence(),
        ];
    }
}