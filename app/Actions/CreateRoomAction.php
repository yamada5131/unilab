<?php

namespace App\Actions;

use App\Models\Room;

final class CreateRoomAction
{
    public function handle(array $data): Room
    {
        return Room::query()->create($data);
    }
}
