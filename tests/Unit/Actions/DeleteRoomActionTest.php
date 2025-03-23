<?php

use App\Actions\DeleteRoomAction;
use App\Models\Room;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('it deletes a room', function () {
    // Arrange
    $room = Room::query()->create([
        'name' => 'Room to Delete',
        'grid_rows' => 5,
        'grid_cols' => 4,
    ]);

    $action = new DeleteRoomAction();

    // Act
    $action->handle($room->id);

    // Assert
    expect(Room::query()->find($room->id))->toBeNull();
});
