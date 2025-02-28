<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine_metric extends Model
{
    /** @use HasFactory<\Database\Factories\MachineMetricFactory> */
    use HasFactory;

    use HasUuids;
}
