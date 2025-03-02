<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('commands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('machine_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->text('command_type');
            $table->jsonb('payload')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'done', 'error'])->default('pending');
            $table->timestampTz('completed_at')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commands');
    }
};
