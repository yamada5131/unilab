<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Room;

final class DeleteRoomAction
{
    public function handle(string $id): void
    {
        $room = Room::query()->findOrFail($id);
        $room->delete();
    }
}
