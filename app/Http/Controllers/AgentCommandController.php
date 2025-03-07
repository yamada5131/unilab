<?php

namespace App\Http\Controllers;

use App\Models\Command;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AgentCommandController extends Controller
{
    public function updateStatus(Request $request)
    {
        // Log toàn bộ request data
        Log::info('Request received', [
            'data' => $request->all(),
            'headers' => $request->headers->all(),
        ]);
        // Validate the incoming request
        $validated = $request->validate([
            'command_id' => 'required|uuid|exists:commands,id',
            'status' => ['required', Rule::in(['done', 'error'])],
            'message' => 'sometimes|nullable|string',
        ]);

        try {
            // Find the command
            $command = Command::findOrFail($validated['command_id']);

            // Update command status and completion time
            $command->status = $validated['status'];
            $command->completed_at = Carbon::now();

            // If message is provided, add it to the payload
            if (isset($validated['message'])) {
                $payload = $command->payload ?? [];
                if (! is_array($payload)) {
                    $payload = [];
                }
                $payload['result_message'] = $validated['message'];
                $command->payload = $payload;
            }

            // Save changes
            $command->save();

            return response()->json([
                'success' => true,
                'message' => 'Command status updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update command status: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update command status',
            ], 500);
        }
    }
}
