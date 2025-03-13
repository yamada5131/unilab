<?php

namespace App\Models;

use App\Enums\CommandStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Command extends Model
{
    use HasFactory;
    use HasUuids;

    protected $guarded = [];

    public function casts(): array
    {
        return [
            'status' => CommandStatus::class,

        ];
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }
}
