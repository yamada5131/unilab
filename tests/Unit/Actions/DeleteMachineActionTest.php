<?php

use App\Actions\DeleteMachineAction;
use App\Models\Machine;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('it deletes a machine', function () {
    // Arrange
    $machine = Machine::factory()->create();

    $action = new DeleteMachineAction();

    // Act
    $deletedMachine = $action->handle($machine->id);

    // Assert
    expect($deletedMachine)->toBeInstanceOf(Machine::class);
    expect(Machine::query()->find($machine->id))->toBeNull();
});
