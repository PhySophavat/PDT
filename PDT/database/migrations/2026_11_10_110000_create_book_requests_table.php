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
        Schema::create('book_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('requested_by_name');
            $table->string('requested_by_role')->default('student'); // student, teacher, admin
            $table->string('grade')->nullable(); // 7-12, or general
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('subject_name')->nullable();
            $table->foreignId('course_subject_id')->nullable()->constrained('course_subjects')->nullOnDelete();
            $table->foreignId('book_category_id')->nullable()->constrained('book_categories')->nullOnDelete();
            $table->string('level')->default('basic'); // basic, advanced, literature
            $table->text('file_url')->nullable(); // Google Drive, Telegram, PDF web link
            $table->string('attachment_path')->nullable(); // Local uploaded file path if any
            $table->text('note')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('admin_feedback')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('published_book_id')->nullable()->constrained('books')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('requested_by_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_requests');
    }
};

