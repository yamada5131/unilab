<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Command;

final class UpdateCommandStatusAction
{
    public function handle(string $id, array $data): Command
    {
        $command = Command::query()->findOrFail($id);

        $command->update([
            'status' => $data['status'],
            'result' => $data['result'] ?? $command->result,
            'error' => $data['error'] ?? $command->error,
            'completed_at' => in_array($data['status'], ['done', 'failed', 'cancelled']) ? now() : null,
        ]);

        return $command;
    }
}
