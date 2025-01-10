<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Lab '.$this->faker->unique()->numberBetween(1, 100),
            'capacity' => 48, // Fixed capacity of 48 for each room
            'status' => $this->faker->randomElement(['vacant', 'in use', 'under maintenance', 'reserved']),
        ];
    }
}
