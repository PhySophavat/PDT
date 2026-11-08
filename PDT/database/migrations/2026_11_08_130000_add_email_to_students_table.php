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
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'email')) {
                $table->string('email')->nullable()->after('user_id');
            }
        });

        try {
            Schema::table('students', function (Blueprint $table) {
                $table->unique('email', 'students_email_unique');
            });
        } catch (\Throwable $e) {
            // Index might already exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'email')) {
                $table->dropColumn('email');
            }
        });
    }
};

