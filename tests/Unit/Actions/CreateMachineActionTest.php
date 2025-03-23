<?php

use App\Actions\CreateMachineAction;
use App\Models\Machine;
use Illuminate\Support\Carbon;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('it creates a machine', function () {
    // Arrange
    $room = \App\Models\Room::factory()->create();
    $action = new CreateMachineAction();
    $data = [
        'name' => 'Test Machine',
        'ip_address' => '192.168.1.1',
        'mac_address' => '00:11:22:33:44:55',
        'room_id' => $room->id,
        'pos_row' => 1,
        'pos_col' => 1,
    ];

    // Act
    Carbon::setTestNow('2025-01-01 00:00:00');
    $machine = $action->handle($data);

    // Assert
    expect($machine)->toBeInstanceOf(Machine::class);
    expect($machine->name)->toBe('Test Machine');
    expect($machine->ip_address)->toBe('192.168.1.1');
    expect($machine->mac_address)->toBe('00:11:22:33:44:55');
    expect($machine->room_id)->toBe($room->id);
    expect($machine->last_seen->toDateTimeString())->toBe('2025-01-01 00:00:00');
});
