<?php

declare(strict_types=1);

use App\Enums\CommandStatus;
use App\Models\Command;
use App\Models\Machine;
use App\Models\Room;

test('to array', function () {
    $command = Command::factory()->create()->fresh();

    expect(array_keys($command->toArray()))->toBe([
        'id',
        'machine_id',
        'room_id',
        'command_type',
        'payload',
        'status',
        'is_group_command',
        'completed_at',
        'created_at',
        'updated_at',
        'error',
    ]);
});

test('status', function () {
    $command = Command::factory()->create()->fresh();

    expect($command->status)->toBe(CommandStatus::PENDING);
});

test('command belongs to machine', function () {
    $machine = Machine::factory()->create();
    $command = Command::factory()->create(['machine_id' => $machine->id]);

    expect($command->machine)->toBeInstanceOf(Machine::class);
    expect($command->machine->id)->toBe($machine->id);
});

test('command belongs to room', function () {
    $room = Room::factory()->create();
    $command = Command::factory()->create(['room_id' => $room->id]);

    expect($command->room)->toBeInstanceOf(Room::class);
    expect($command->room->id)->toBe($room->id);
});

test('command can be marked as completed', function () {
    $command = Command::factory()->create(['status' => CommandStatus::PENDING]);
    
    $command->markAsCompleted();
    
    expect($command->status)->toBe(CommandStatus::COMPLETED);
    expect($command->completed_at)->not->toBeNull();
});

test('command can be marked as failed', function () {
    $command = Command::factory()->create(['status' => CommandStatus::PENDING]);
    
    $command->markAsFailed('Test error message');
    
    expect($command->status)->toBe(CommandStatus::FAILED);
    expect($command->error)->toBe('Test error message');
});

test('command can store json payload', function () {
    $payload = ['key' => 'value', 'nested' => ['data' => true]];
    
    $command = Command::factory()->create(['payload' => $payload]);
    $freshCommand = Command::find($command->id);
    
    expect($freshCommand->payload)->toBe($payload);
});
