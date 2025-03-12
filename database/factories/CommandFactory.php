<?php

namespace Database\Factories;

use App\Enums\CommandStatus;
use App\Models\Machine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Command>
 */
class CommandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'machine_id' => Machine::factory(), // Tự động tạo Machine và sử dụng ID
            'command_type' => $this->faker->randomElement(['SHUTDOWN', 'RESTART', 'INSTALL', 'UPDATE']),
            'payload' => null,
            'status' => CommandStatus::PENDING,
            'completed_at' => null,
            'created_at' => $this->faker->dateTime(),
            'updated_at' => $this->faker->dateTime(),
        ];
    }
}
