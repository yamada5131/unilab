<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Command extends Model
{
    /** @use HasFactory<\Database\Factories\CommandFactory> */
    use HasFactory;

    use HasUuids;

    protected $guarded = [];
}
