<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateMachineAction;
use App\Actions\DeleteMachineAction;
use App\Actions\UpdateMachineAction;
use App\Http\Requests\DeleteMachineRequest;
use App\Http\Requests\StoreMachineRequest;
use App\Http\Requests\UpdateMachineRequest;
use Illuminate\Http\RedirectResponse;

final class MachineController extends Controller
{
    public function store(
        StoreMachineRequest $request,
        CreateMachineAction $action
    ): RedirectResponse {
        $machine = $action->handle($request->validated());

        return to_route('rooms.show', $machine->room_id);
    }

    public function update(
        UpdateMachineRequest $request,
        string $id,
        UpdateMachineAction $action
    ): RedirectResponse {
        $machine = $action->handle($id, $request->validated());

        return to_route('rooms.show', $machine->room_id);
    }

    public function destroy(
        DeleteMachineRequest $request,
        string $id,
        DeleteMachineAction $action
    ): RedirectResponse {
        $machine = $action->handle($id);

        return to_route('rooms.show', $machine->room_id);
    }
}
