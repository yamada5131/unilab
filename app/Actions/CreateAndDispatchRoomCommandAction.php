<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Command;
use App\Services\RabbitMQService;
use Exception;
use Illuminate\Support\Facades\Log;

final readonly class CreateAndDispatchRoomCommandAction
{
    public function __construct(
        private RabbitMQService $rabbitMQService
    ) {}

    public function handle(array $data): array
    {
        // Create group command record
        $groupCommand = Command::query()->create([
            'room_id' => $data['room_id'],
            'command_type' => $data['command_type'],
            'params' => $data['params'] ?? [],
            'status' => 'pending',
            'priority' => $data['priority'] ?? 5,
            'is_group_command' => true,
        ]);

        // Prepare the command data
        $commandData = [
            'id' => $groupCommand->id,
            'type' => $groupCommand->command_type,
            'params' => $groupCommand->params,
            'issued_at' => now()->toIso8601String(),
            'expires_in' => $data['expires_in'] ?? 600, // Default 10 minutes
            'priority' => $groupCommand->priority,
            'requires_confirmation' => in_array($groupCommand->command_type, ['SHUTDOWN', 'RESTART']),
        ];

        try {
            // Send command to all machines in room
            $sendResult = $this->rabbitMQService->sendCommandToRoom(
                $data['room_id'],
                $commandData
            );

            if ($sendResult) {
                $groupCommand->update(['status' => 'sent']);

                Log::info("Group command ID {$groupCommand->id} of type {$groupCommand->command_type} sent to room {$data['room_id']}");

                return [
                    'success' => true,
                    'message' => 'Command sent to all machines in room',
                    'command' => $groupCommand,
                    'status_code' => 200,
                ];
            }

            $groupCommand->update(['status' => 'failed', 'error' => 'Failed to send to message queue']);
            Log::error("Failed to send group command {$groupCommand->id} to message queue");

            return [
                'success' => false,
                'message' => 'Failed to send command to message queue',
                'command' => $groupCommand,
                'status_code' => 500,
            ];

        } catch (Exception $e) {
            $groupCommand->update(['status' => 'failed', 'error' => $e->getMessage()]);
            Log::error("Exception sending group command {$groupCommand->id}: ".$e->getMessage());

            return [
                'success' => false,
                'message' => 'Error sending command: '.$e->getMessage(),
                'command' => $groupCommand,
                'status_code' => 500,
            ];
        }
    }
}
