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
        Schema::create('machines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('room_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->text('name');
            $table->macAddress('mac_address')->unique();
            $table->ipAddress('ip_address');
            $table->integer('pos_row');
            $table->integer('pos_col');
            $table->boolean('is_online')->default(false);
            $table->timestampTz('last_seen');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
