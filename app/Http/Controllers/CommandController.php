<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessComputerCommand;
use App\Models\Command;
use Illuminate\Http\Request;

class CommandController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request and insert the command into the database
        $command = Command::create($request->validate([
            'machine_id' => 'required|exists:App\Models\Machine,id',
            'command_type' => 'required|string',
        ]));

        // Dispatch the job to the queue
        ProcessComputerCommand::dispatch($command)
            ->onConnection('rabbitmq')
            ->onQueue("command.machine.{$command->machine_id}");

        return redirect()->back();
    }
}
