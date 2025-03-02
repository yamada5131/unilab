<?php

namespace App\Jobs;

use App\Models\Command;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessComputerCommand implements ShouldQueue
{
    use Queueable;

    protected $commandData;

    /**
     * Create a new job instance.
     */
    public function __construct(
        Command $command
    ) {
        $this->commandData = [
            'id' => $command->id,
            'machine_id' => $command->machine_id,
            'command_type' => $command->command_type,
        ];
    }

    /**
     * Execute the job.
     */
    public function handle(): void {}
}
