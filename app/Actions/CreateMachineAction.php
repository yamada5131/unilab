<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Machine;
use Illuminate\Support\Carbon;

final class CreateMachineAction
{
    public function handle(array $data): Machine
    {
        $data['last_seen'] = Carbon::now();

        return Machine::create($data);
    }
}
