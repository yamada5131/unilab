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
        $machine = Machine::with('room')->findOrFail($validated['machine_id']);

        // Prepare the command data
        $commandData = [
            'id' => $command->id,
            'command_type' => $command->command_type,
        ];
        try {
            // Send command to RabbitMQ
            $sendResult = $this->rabbitMQService->sendCommandToComputer(
                $machine->id,
                $machine->room->id,
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
}
