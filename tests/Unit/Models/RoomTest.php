<?php

use App\Models\Machine;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('room has many machines', function () {
    // Arrange
    $room = Room::factory()->create();

    // Create two machines for this room
    Machine::factory()->count(2)->create([
        'room_id' => $room->id,
    ]);

    // Act & Assert
    expect($room->machines)->toHaveCount(2);
    expect($room->machines->first())->toBeInstanceOf(Machine::class);
});

test('room attributes', function () {
    // Arrange
    $room = Room::factory()->create([
        'name' => 'Test Room',
    ]);

    // Act & Assert
    expect($room->name)->toBe('Test Room');
    expect($room->grid_rows)->toBeGreaterThan(0);
    expect($room->grid_cols)->toBeGreaterThan(0);
});

test('room uses uuid as primary key', function () {
    // Arrange
    $room = Room::factory()->create();

    // Act & Assert
    expect($room->id)->toBeString();
    expect(strlen($room->id))->toBe(36); // UUID length
});
