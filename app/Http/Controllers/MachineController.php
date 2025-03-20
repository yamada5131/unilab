<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMachineRequest;
use App\Http\Requests\UpdateMachineRequest;
use App\Models\Machine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;

class MachineController extends Controller
{
    public function store(StoreMachineRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['last_seen'] = Carbon::now();

        Machine::create($validated);

        return to_route('rooms.show', $validated['room_id']);
    }

    public function update(UpdateMachineRequest $request, string $id): RedirectResponse
    {
        $machine = Machine::findOrFail($id);

        $validated = $request->validated();

        $machine->update($validated);

        return to_route('rooms.show', $machine->room_id);
    }

    public function destroy(string $id): RedirectResponse
    {
        $machine = Machine::findOrFail($id);
        $machine->delete();

        return to_route('rooms.show', $machine->room_id);
    }
}
