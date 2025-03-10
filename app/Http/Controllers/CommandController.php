<?php

namespace App\Http\Controllers;

use App\Models\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;

class CommandController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request and insert the command into the database
        $command = Command::create($request->validate([
            'machine_id' => 'required|exists:App\Models\Machine,id',
            'command_type' => 'required|string',
        ]));

        // Prepare the command data
        $commandData = [
            'id' => $command->id,
            'machine_id' => $command->machine_id,
            'command_type' => $command->command_type,
        ];

        Queue::connection('rabbitmq')->pushRaw(json_encode([
            // 'job' => 'App\\Jobs\\ProcessComputerCommand@handle',
            'data' => $commandData,
        ]), "command.machine.{$command->machine_id}");

        return redirect()->back();
    }
}
