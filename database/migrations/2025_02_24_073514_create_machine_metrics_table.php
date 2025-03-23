<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machine_metrics', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('machine_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->float('cpu_usage')->nullable();
            $table->float('ram_usage')->nullable();
            $table->float('disk_usage')->nullable();
            $table->timestampsTz();
        });
    }
};
