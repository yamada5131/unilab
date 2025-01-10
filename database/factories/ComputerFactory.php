<?php

namespace Database\Factories;

use App\Models\Computer;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Computer>
 */
class ComputerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word().' PC',
            'status' => $this->faker->randomElement(['on', 'off', 'standby']),
            'room_id' => Room::factory(),
            'hardware_specifications' => [
                'CPU' => $this->faker->randomElement(['Intel i5', 'Intel i7', 'AMD Ryzen 5', 'AMD Ryzen 7']),
                'RAM' => $this->faker->randomElement(['8GB', '16GB', '32GB']),
                'Storage' => $this->faker->randomElement(['256GB SSD', '512GB SSD', '1TB HDD']),
            ],
        ];
    }
}
