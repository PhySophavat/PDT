<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to add high-performance database indexes.
     */
    public function up(): void
    {
        // 1. attendances table: Speed up weekly trends, daily attendance, and class reports
        Schema::table('attendances', function (Blueprint $table) {
            $table->index(['class_name', 'date'], 'attendances_class_name_date_idx');
            $table->index(['date', 'status'], 'attendances_date_status_idx');
        });

        // 2. students table: Speed up filtering students by class grade and linking to user account
        Schema::table('students', function (Blueprint $table) {
            $table->index('grade', 'students_grade_idx');
            $table->index('user_id', 'students_user_id_idx');
            $table->index('status', 'students_status_idx');
        });

        // 3. examination_scores table: Speed up ranking sheet and individual report cards
        Schema::table('examination_scores', function (Blueprint $table) {
            $table->index('student_id', 'exam_scores_student_id_idx');
            $table->index('class_name', 'exam_scores_class_name_idx');
        });

        // 4. timetables table: Speed up schedule lookups by class and teacher
        Schema::table('timetables', function (Blueprint $table) {
            $table->index('class_name', 'timetables_class_name_idx');
            $table->index('teacher_id', 'timetables_teacher_id_idx');
        });

        // 5. book_borrows table: Speed up library dashboard and student borrowing history
        Schema::table('book_borrows', function (Blueprint $table) {
            $table->index(['student_id', 'status'], 'book_borrows_student_status_idx');
            $table->index('book_id', 'book_borrows_book_id_idx');
            $table->index('status', 'book_borrows_status_idx');
        });

        // 6. announcements table: Speed up active notification & bulletin feeds
        Schema::table('announcements', function (Blueprint $table) {
            $table->index(['is_active', 'created_at'], 'announcements_active_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('attendances_class_name_date_idx');
            $table->dropIndex('attendances_date_status_idx');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('students_grade_idx');
            $table->dropIndex('students_user_id_idx');
            $table->dropIndex('students_status_idx');
        });

        Schema::table('examination_scores', function (Blueprint $table) {
            $table->dropIndex('exam_scores_student_id_idx');
            $table->dropIndex('exam_scores_class_name_idx');
        });

        Schema::table('timetables', function (Blueprint $table) {
            $table->dropIndex('timetables_class_name_idx');
            $table->dropIndex('timetables_teacher_id_idx');
        });

        Schema::table('book_borrows', function (Blueprint $table) {
            $table->dropIndex('book_borrows_student_status_idx');
            $table->dropIndex('book_borrows_book_id_idx');
            $table->dropIndex('book_borrows_status_idx');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->dropIndex('announcements_active_created_idx');
        });
    }
};

