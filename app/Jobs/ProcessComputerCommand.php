<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessComputerCommand implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $computerId,
        private string $command,
        private array $params = []
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void {}
}
