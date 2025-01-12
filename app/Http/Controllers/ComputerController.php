<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessComputerCommand;
use Illuminate\Http\Request;

class ComputerController extends Controller
{
    public function sendCommand(Request $request, $id)
    {
        $validated = $request->validate([
            'command' => 'required|string',
            'params' => 'array|nullable',
        ]);

        ProcessComputerCommand::dispatch(
            $id,
            $validated['command'],
        )->onQueue($id);

        return response()->json(['message' => 'Command queued']);
    }
}
