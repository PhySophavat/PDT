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
        Schema::table('examination_scores', function (Blueprint $table) {
            $table->unique(['examination_id', 'student_id', 'course_subject_id'], 'uniq_exam_student_subject');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('examination_scores', function (Blueprint $table) {
            $table->dropUnique('uniq_exam_student_subject');
        });
    }
};

