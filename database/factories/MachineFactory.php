<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MachineFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'room_id' => Room::factory(),
            'name' => $this->faker->word.'-PC-'.$this->faker->numberBetween(1, 100),
            'mac_address' => $this->faker->macAddress(),
            'ip_address' => $this->faker->ipv4(),
            'pos_row' => $this->faker->numberBetween(0, 10),
            'pos_col' => $this->faker->numberBetween(0, 10),
            'is_online' => $this->faker->boolean(20), // 20% chance to be online
            'last_seen' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
