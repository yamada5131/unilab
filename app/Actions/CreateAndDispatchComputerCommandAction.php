<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Command;
use App\Models\Machine;
use App\Services\RabbitMQService;
use Exception;
use Illuminate\Support\Facades\Log;

final class CreateAndDispatchComputerCommandAction
{
    public function __construct(
        protected RabbitMQService $rabbitMQService
    ) {}

    public function handle(array $data): array
    {
        // Create command record
        $command = Command::query()->create([
            'machine_id' => $data['machine_id'],
            'command_type' => $data['command_type'],
            'status' => 'pending',
        ]);

        // Get machine and room information
        $machine = Machine::query()
            ->select(['id', 'room_id'])
            ->findOrFail($data['machine_id']);

        // Prepare the command data
        $commandData = [
            'id' => $command->id,
            'command_type' => $command->command_type,
        ];

        try {
            // Send command to RabbitMQ
            $sendResult = $this->rabbitMQService->sendCommandToComputer(
                $machine->id,
                $machine->room_id,
                $commandData
            );

            if ($sendResult) {
                // Update command status to 'completed'
                $command->update(['status' => 'completed']);

                Log::info("Command ID {$command->id} of type {$command->command_type} sent to machine {$machine->id}");

                return [
                    'success' => true,
                    'message' => 'Command sent successfully',
                    'command' => $command,
                    'status_code' => 200,
                ];
            }

            $command->update(['status' => 'failed', 'error' => 'Failed to send to message queue']);
            Log::error("Failed to send command {$command->id} to message queue");

            return [
                'success' => false,
                'message' => 'Failed to send command to message queue',
                'command' => $command,
                'status_code' => 500,
            ];

        } catch (Exception $e) {
            $command->update(['status' => 'failed', 'error' => $e->getMessage()]);
            Log::error("Exception sending command {$command->id}: ".$e->getMessage());

            return [
                'success' => false,
                'message' => 'Error sending command: '.$e->getMessage(),
                'command' => $command,
                'status_code' => 500,
            ];
        }
    }
}
