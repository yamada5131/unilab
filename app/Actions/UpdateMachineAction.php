<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Machine;

final class UpdateMachineAction
{
    public function handle(string $id, array $data): Machine
    {
        $machine = Machine::query()->findOrFail($id);
        $machine->update($data);

        return $machine;
    }
}
