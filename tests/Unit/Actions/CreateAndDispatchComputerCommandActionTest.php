<?php

use App\Actions\CreateAndDispatchComputerCommandAction;
use App\Models\Command;
use App\Models\Machine;
use App\Services\RabbitMQService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;

uses(RefreshDatabase::class);

test('it creates and dispatches a computer command successfully', function () {
    // Arrange
    $machine = Machine::factory()->create();

    $data = [
        'machine_id' => $machine->id,
        'command_type' => 'restart',
    ];

    // Mock RabbitMQService
    $rabbitMQService = mock(RabbitMQService::class);
    $rabbitMQService->shouldReceive('sendCommandToComputer')
        ->once()
        ->andReturn(true);

    $action = new CreateAndDispatchComputerCommandAction($rabbitMQService);

    // Act
    $result = $action->handle($data);

    // Assert
    expect($result['success'])->toBeTrue();
    expect($result['status_code'])->toBe(200);
    expect($result['command'])->toBeInstanceOf(Command::class);
    expect($result['command']->status->value)->toBe('completed');
});

test('it handles failure when sending command to RabbitMQ', function () {
    // Arrange
    $machine = Machine::factory()->create();

    $data = [
        'machine_id' => $machine->id,
        'command_type' => 'restart',
    ];

    // Mock RabbitMQService
    $rabbitMQService = mock(RabbitMQService::class);
    $rabbitMQService->shouldReceive('sendCommandToComputer')
        ->once()
        ->andReturn(false);

    $action = new CreateAndDispatchComputerCommandAction($rabbitMQService);

    // Act
    $result = $action->handle($data);

    // Assert
    expect($result['success'])->toBeFalse();
    expect($result['status_code'])->toBe(500);
    expect($result['command'])->toBeInstanceOf(Command::class);
    expect($result['command']->status->value)->toBe('failed');
});

test('it handles exceptions when sending command', function () {
    // Arrange
    $machine = Machine::factory()->create();

    $data = [
        'machine_id' => $machine->id,
        'command_type' => 'restart',
    ];

    // Mock RabbitMQService
    $rabbitMQService = mock(RabbitMQService::class);
    $rabbitMQService->shouldReceive('sendCommandToComputer')
        ->once()
        ->andThrow(new \Exception('Test exception'));

    $action = new CreateAndDispatchComputerCommandAction($rabbitMQService);

    // Act
    $result = $action->handle($data);

    // Assert
    expect($result['success'])->toBeFalse();
    expect($result['status_code'])->toBe(500);
    expect($result['command'])->toBeInstanceOf(Command::class);
    expect($result['command']->status->value)->toBe('failed');
    expect(json_decode($result['command']->payload, true)['error'])->toBe('Test exception');
});
