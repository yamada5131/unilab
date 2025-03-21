<?php

namespace Database\Seeders;

use App\Models\Computer;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Rooms
        $rooms = Room::factory()->count(10)->create();

        // Create Users
        User::factory()->count(10)->create();

        // Create Computers and assign them to Rooms
        foreach ($rooms as $room) {
            $computers = Computer::factory()->count(48)->create([
                'room_id' => $room->id,
            ]);
        }

    }
}
