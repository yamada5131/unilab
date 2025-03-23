<?php

use App\Actions\CreateRoomAction;
use App\Models\Room;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('it creates a room', function () {
    // Arrange
    $action = new CreateRoomAction();
    $data = [
        'name' => 'Test Room',
        'grid_rows' => 5,
        'grid_cols' => 5,
    ];

    // Act
    $room = $action->handle($data);

    // Assert
    expect($room)->toBeInstanceOf(Room::class);
    expect($room->name)->toBe('Test Room');
    expect($room->grid_rows)->toBe(5);
    expect($room->grid_cols)->toBe(5);
});
