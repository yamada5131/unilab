<?php

namespace App\Actions;

use App\Models\Machine;

final class DeleteMachineAction
{
    public function handle(string $id): Machine
    {
        $machine = Machine::query()->findOrFail($id);
        $machine->delete();

        return $machine;
    }
}
