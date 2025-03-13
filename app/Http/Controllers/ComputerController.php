<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComputerRequest;
use App\Http\Requests\UpdateComputerRequest;
use App\Jobs\ProcessComputerCommand;
use App\Models\Machine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ComputerController extends Controller
{
    public function store(StoreComputerRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['last_seen'] = Carbon::now();

        Machine::create($validated);

        return redirect()->back();
    }

    public function update(UpdateComputerRequest $request, string $id): RedirectResponse
    {
        $machine = Machine::findOrFail($id);

        $validated = $request->validated();

        $machine->update($validated);

        return redirect()->back();
    }

    public function destroy(string $id): RedirectResponse
    {
        $machine = Machine::findOrFail($id);
        $machine->delete();

        return to_route('rooms.show', $machine->room_id);
    }

    public function sendCommand(Request $request, string $id)
    {
        $validated = $request->validate([
            'command' => 'required|string',
            'params' => 'array|nullable',
        ]);

        ProcessComputerCommand::dispatch(
            $id,
            $validated['command'],
        )->onQueue($id.'.#');

        return redirect()->back();
    }
}
