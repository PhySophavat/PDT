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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('user_name')->nullable();
            $table->string('user_role')->nullable();
            $table->string('action', 50)->index(); // CREATED, UPDATED, DELETED, LOGIN, FAILED_LOGIN, etc.
            $table->string('module', 100)->index(); // ឈ្មោះផ្នែកជាភាសាខ្មែរ (ឧ. ការប្រឡង, បណ្ណាល័យ, វត្តមានសិស្ស)
            $table->string('target_id', 100)->nullable()->index();
            $table->text('description')->nullable();
            $table->string('device_name', 150)->nullable(); // ឧ. "Windows 11 - Chrome"
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('payload')->nullable(); // JSON: old_values & new_values
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};

