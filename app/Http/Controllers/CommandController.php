<?php

namespace App\Http\Controllers;

use App\Models\Command;
use App\Models\Machine;
use App\Services\RabbitMQService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommandController extends Controller
{
    /**
     * Constructor with dependency injection for RabbitMQService
     */
    public function __construct(protected RabbitMQService $rabbitMQService) {}

    public function storeComputerCommand(Request $request)
    {
        $validated = $request->validate([
            'machine_id' => 'required|exists:App\Models\Machine,id',
            'command_type' => 'required|string',
        ]);
        // Validate the request and insert the command into the database
        $command = Command::create(array_merge($validated, ['status' => 'pending']));

        // Get machine and room information
        $machine = Machine::select(['id', 'room_id'])->findOrFail($validated['machine_id']);

        // Prepare the command data
        $commandData = [
            'id' => $command->id,
            'command_type' => $command->command_type,
            // ...
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

                return response()->json([
                    'success' => true,
                    'message' => 'Command sent successfully',
                    'command' => $command,
                ], 200);
            } else {
                $command->update(['status' => 'failed', 'error' => 'Failed to send to message queue']);

                Log::error("Failed to send command {$command->id} to message queue");

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send command to message queue',
                    'command' => $command,
                ], 500);
            }
        } catch (\Exception $e) {
            $command->update(['status' => 'failed', 'error' => $e->getMessage()]);

            Log::error("Exception sending command {$command->id}: ".$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error sending command: '.$e->getMessage(),
                'command' => $command,
            ], 500);
        }
    }

    /**
     * Store and send command to all machines in a room
     */
    public function storeRoomCommand(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'command_type' => 'required|string',
            'params' => 'sometimes|array',
            'priority' => 'sometimes|integer|min:1|max:10',
            'expires_in' => 'sometimes|integer|min:60|max:86400', // 1 minute to 24 hours
        ]);

        // Create group command record
        $groupCommand = Command::create([
            'room_id' => $validatedData['room_id'],
            'command_type' => $validatedData['command_type'],
            'params' => $validatedData['params'] ?? [],
            'status' => 'pending',
            'priority' => $validatedData['priority'] ?? 5,
            'is_group_command' => true,
        ]);

        // Prepare the command data
        $commandData = [
            'id' => $groupCommand->id,
            'type' => $groupCommand->command_type,
            'params' => $groupCommand->params,
            'issued_at' => now()->toIso8601String(),
            'expires_in' => $validatedData['expires_in'] ?? 600, // Default 10 minutes
            'priority' => $groupCommand->priority,
            'requires_confirmation' => in_array($groupCommand->command_type, ['SHUTDOWN', 'RESTART']),
        ];

        try {
            // Send command to all machines in room
            $sendResult = $this->rabbitMQService->sendCommandToRoom(
                $validatedData['room_id'],
                $commandData
            );

            if ($sendResult) {
                $groupCommand->update(['status' => 'sent']);

                Log::info("Group command ID {$groupCommand->id} of type {$groupCommand->command_type} sent to room {$validatedData['room_id']}");

                return response()->json([
                    'success' => true,
                    'message' => 'Command sent to all machines in room',
                    'command' => $groupCommand,
                ], 200);
            } else {
                $groupCommand->update(['status' => 'failed', 'error' => 'Failed to send to message queue']);

                Log::error("Failed to send group command {$groupCommand->id} to message queue");

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send command to message queue',
                    'command' => $groupCommand,
                ], 500);
            }
        } catch (\Exception $e) {
            $groupCommand->update(['status' => 'failed', 'error' => $e->getMessage()]);

            Log::error("Exception sending group command {$groupCommand->id}: ".$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error sending command: '.$e->getMessage(),
                'command' => $groupCommand,
            ], 500);
        }
    }

    /**
     * Get command status
     */
    public function show($id)
    {
        $command = Command::findOrFail($id);

        return response()->json([
            'success' => true,
            'command' => $command,
        ]);
    }

    /**
     * Update command status (used by agent to report back)
     */
    public function updateStatus(Request $request, $id)
    {
        $command = Command::findOrFail($id);

        $validatedData = $request->validate([
            'status' => 'required|string|in:running,done,failed,cancelled',
            'result' => 'sometimes|array',
            'error' => 'sometimes|string',
        ]);

        $command->update([
            'status' => $validatedData['status'],
            'result' => $validatedData['result'] ?? $command->result,
            'error' => $validatedData['error'] ?? $command->error,
            'completed_at' => in_array($validatedData['status'], ['done', 'failed', 'cancelled']) ? now() : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Command status updated',
            'command' => $command,
        ]);
    }
}
