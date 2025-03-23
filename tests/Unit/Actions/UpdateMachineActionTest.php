<?php

use App\Actions\UpdateMachineAction;
use App\Models\Machine;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('it updates a machine', function () {
    // Arrange
    $machine = Machine::factory()->create([
        'name' => 'Old Machine Name',
    ]);

    $action = new UpdateMachineAction();
    $data = [
        'name' => 'New Machine Name',
        'ip_address' => '192.168.1.2',
        'mac_address' => '00:11:22:33:44:66',
    ];

    // Act
    $updatedMachine = $action->handle($machine->id, $data);

    // Assert
    expect($updatedMachine)->toBeInstanceOf(Machine::class);
    expect($updatedMachine->name)->toBe('New Machine Name');
    expect($updatedMachine->ip_address)->toBe('192.168.1.2');
    expect($updatedMachine->mac_address)->toBe('00:11:22:33:44:66');
    
    // Check old data is gone
    expect(Machine::query()->where('name', 'Old Machine Name')->exists())->toBeFalse();
});
