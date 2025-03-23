<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CommandStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Command extends Model
{
    use HasFactory;
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'status' => CommandStatus::class,
        'payload' => 'array',
        'is_group_command' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }
    
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
    
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => CommandStatus::COMPLETED,
            'completed_at' => Carbon::now(),
        ]);
    }
    
    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => CommandStatus::FAILED,
            'error' => $errorMessage,
        ]);
    }
}
