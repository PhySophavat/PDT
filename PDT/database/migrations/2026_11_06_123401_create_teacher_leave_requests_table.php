<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('teacher_leave_requests')) {
            Schema::create('teacher_leave_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('teacher_id')->nullable();
                $table->string('teacher_name');
                $table->string('teacher_code')->nullable();
                $table->string('specialization')->nullable();
                $table->date('start_date');
                $table->date('end_date');
                $table->integer('days_count')->default(1);
                $table->string('leave_type')->default('ឈឺ');
                $table->text('reason');
                $table->string('phone')->nullable();
                $table->string('attachment_path')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->text('rejection_reason')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamps();

                $table->index(['status', 'start_date']);
                $table->index(['teacher_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_leave_requests');
    }
};

