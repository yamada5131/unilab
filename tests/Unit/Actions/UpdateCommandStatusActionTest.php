<?php

use App\Actions\UpdateCommandStatusAction;
use App\Models\Command;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it updates command status to done', function () {
    // Arrange
    $machine = \App\Models\Machine::factory()->create();
    $command = Command::query()->create([
        'machine_id' => $machine->id,
        'command_type' => 'RESTART',
        'status' => 'pending',
    ]);

    $action = new UpdateCommandStatusAction();
    $data = [
        'status' => 'completed',
        'result' => 'Command executed successfully',
    ];

    // Act
    $updatedCommand = $action->handle($command->id, $data);

    // Assert
    expect($updatedCommand)->toBeInstanceOf(Command::class);
    expect($updatedCommand->status->value)->toBe('completed');
    expect(json_decode($updatedCommand->payload, true)['result'])->toBe('Command executed successfully');
    expect($updatedCommand->completed_at)->not->toBeNull();
});

test('it updates command status to failed', function () {
    // Arrange
    $machine = \App\Models\Machine::factory()->create();
    $command = Command::query()->create([
        'machine_id' => $machine->id,
        'command_type' => 'RESTART',
        'status' => 'pending',
    ]);

    $action = new UpdateCommandStatusAction();
    $data = [
        'status' => 'failed',
        'error' => 'Permission denied',
    ];

    // Act
    $updatedCommand = $action->handle($command->id, $data);

    // Assert
    expect($updatedCommand)->toBeInstanceOf(Command::class);
    expect($updatedCommand->status->value)->toBe('failed');
    expect(json_decode($updatedCommand->payload, true)['error'])->toBe('Permission denied');
    expect($updatedCommand->completed_at)->not->toBeNull();
});

test('it updates command status to in_progress without setting completed_at', function () {
    // Arrange
    $machine = \App\Models\Machine::factory()->create();
    $command = Command::query()->create([
        'machine_id' => $machine->id,
        'command_type' => 'RESTART',
        'status' => 'pending',
    ]);

    $action = new UpdateCommandStatusAction();
    $data = [
        'status' => 'in_progress',
    ];

    // Act
    $updatedCommand = $action->handle($command->id, $data);

    // Assert
    expect($updatedCommand)->toBeInstanceOf(Command::class);
    expect($updatedCommand->status->value)->toBe('in_progress');
    expect($updatedCommand->completed_at)->toBeNull();
});
