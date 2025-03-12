<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Lab '.$this->faker->unique()->numberBetween(1, 100),
            'grid_rows' => $this->faker->numberBetween(1, 10),
            'grid_cols' => $this->faker->numberBetween(1, 10),
        ];
    }
}
