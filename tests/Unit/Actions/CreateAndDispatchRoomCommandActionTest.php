<?php

use App\Actions\CreateAndDispatchRoomCommandAction;
use App\Models\Command;
use App\Models\Room;
use App\Services\RabbitMQService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;

uses(RefreshDatabase::class);

test('it creates and dispatches a room command successfully', function () {
    // Arrange
    $room = \App\Models\Room::factory()->create();
    
    $data = [
        'room_id' => $room->id,
        'command_type' => 'SHUTDOWN',
        'params' => ['delay' => 60],
        'priority' => 10,
    ];

    // Mock RabbitMQService
    $rabbitMQService = mock(RabbitMQService::class);
    $rabbitMQService->shouldReceive('sendCommandToRoom')
        ->once()
        ->andReturn(true);

    $action = new CreateAndDispatchRoomCommandAction($rabbitMQService);

    // Act
    $result = $action->handle($data);

    // Assert
    expect($result['success'])->toBeTrue();
    expect($result['status_code'])->toBe(200);
    expect($result['command'])->toBeInstanceOf(Command::class);
    expect($result['command']->status->value)->toBe('completed');
    expect($result['command']->is_group_command)->toBeTrue();
});

test('it handles failure when sending command to RabbitMQ', function () {
    // Arrange
    $room = \App\Models\Room::factory()->create();
    
    $data = [
        'room_id' => $room->id,
        'command_type' => 'RESTART',
    ];

    // Mock RabbitMQService
    $rabbitMQService = mock(RabbitMQService::class);
    $rabbitMQService->shouldReceive('sendCommandToRoom')
        ->once()
        ->andReturn(false);

    $action = new CreateAndDispatchRoomCommandAction($rabbitMQService);

    // Act
    $result = $action->handle($data);

    // Assert
    expect($result['success'])->toBeFalse();
    expect($result['status_code'])->toBe(500);
    expect($result['command'])->toBeInstanceOf(Command::class);
    expect($result['command']->status->value)->toBe('failed');
});

test('it handles exceptions when sending room command', function () {
    // Arrange
    $room = \App\Models\Room::factory()->create();
    $data = [
        'room_id' => $room->id,
        'command_type' => 'RESTART',
    ];

    // Mock RabbitMQService to throw an exception
    $rabbitMQService = mock(RabbitMQService::class, function (MockInterface $mock) {
        $mock->shouldReceive('sendCommandToRoom')
            ->once()
            ->andThrow(new Exception('Test exception'));
    });

    $action = new CreateAndDispatchRoomCommandAction($rabbitMQService);

    // Act
    $result = $action->handle($data);

    // Assert
    expect($result['success'])->toBeFalse();
    expect($result['status_code'])->toBe(500);
    expect($result['command'])->toBeInstanceOf(Command::class);
    expect($result['command']->status->value)->toBe('failed');
    expect(json_decode($result['command']->payload, true)['error'])->toBe('Test exception');
});
