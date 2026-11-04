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
        if (Schema::hasTable('academic_years')) {
            Schema::table('academic_years', function (Blueprint $table) {
                if (!Schema::hasColumn('academic_years', 'semester_1')) {
                    $table->string('semester_1')->nullable()->default('Oct-Feb');
                }
                if (!Schema::hasColumn('academic_years', 'semester_2')) {
                    $table->string('semester_2')->nullable()->default('Mar-Jul');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->dropColumn(['semester_1', 'semester_2']);
        });
    }
};
