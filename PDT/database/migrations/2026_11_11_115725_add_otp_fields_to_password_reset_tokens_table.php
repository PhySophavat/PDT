<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            if (!Schema::hasColumn('password_reset_tokens', 'otp')) {
                $table->string('otp', 10)->nullable()->after('token');
            }
            if (!Schema::hasColumn('password_reset_tokens', 'attempts')) {
                $table->unsignedTinyInteger('attempts')->default(0)->after('otp');
            }
            if (!Schema::hasColumn('password_reset_tokens', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('attempts');
            }
        });
    }

    public function down(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropColumn(['otp', 'attempts', 'expires_at']);
        });
    }
};
