<?php

use App\Enums\CommandStatus;
use App\Models\Command;
use App\Models\Machine;

test('to array', function () {
    $command = Command::factory()->create()->fresh();

    expect(array_keys($command->toArray()))->toBe([
        'id',
        'machine_id',
        'command_type',
        'payload',
        'status',
        'completed_at',
        'created_at',
        'updated_at',
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
