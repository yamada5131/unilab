<?php

use App\Actions\UpdateRoomAction;
use App\Models\Room;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('it updates a room', function () {
    // Arrange
    $room = Room::factory()->create([
        'name' => 'Old Room Name',
        'grid_rows' => 4,
        'grid_cols' => 4,
    ]);

    $action = new UpdateRoomAction();
    $data = [
        'name' => 'New Room Name',
        'grid_rows' => 6,
        'grid_cols' => 6,
    ];

    // Act
    $updatedRoom = $action->handle($room->id, $data);

    // Assert
    expect($updatedRoom)->toBeInstanceOf(Room::class);
    expect($updatedRoom->name)->toBe('New Room Name');
    expect($updatedRoom->grid_rows)->toBe(6);
    expect($updatedRoom->grid_cols)->toBe(6);
    
    // Check old data is gone
    expect(Room::query()->where('name', 'Old Room Name')->exists())->toBeFalse();
});
