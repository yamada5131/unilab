<?php

namespace App\Actions;

use App\Models\Command;

final class UpdateCommandStatusAction
{
    public function handle(string $id, array $data): Command
    {
        $command = Command::query()->findOrFail($id);

        $command->update([
            'status' => $data['status'],
            'payload' => json_encode([
                'result' => $data['result'] ?? json_decode($command->payload, true)['result'] ?? null,
                'error' => $data['error'] ?? json_decode($command->payload, true)['error'] ?? null
            ]),
            'completed_at' => in_array($data['status'], ['completed', 'failed']) ? now() : null,
        ]);

        return $command;
    }
}
