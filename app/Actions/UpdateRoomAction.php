<?php

namespace App\Actions;

use App\Models\Room;

final class UpdateRoomAction
{
    public function handle(string $id, array $data): Room
    {
        $room = Room::query()->findOrFail($id);
        $room->update($data);

        return $room;
    }
}
