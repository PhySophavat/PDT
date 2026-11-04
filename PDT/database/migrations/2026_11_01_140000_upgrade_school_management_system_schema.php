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
        // 1. Academic Years
        if (!Schema::hasTable('academic_years')) {
            Schema::create('academic_years', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique(); // e.g. "2025-2026"
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->string('semester_1')->nullable()->default('Oct-Feb');
                $table->string('semester_2')->nullable()->default('Mar-Jul');
                $table->boolean('is_current')->default(false);
                $table->string('status')->default('active'); // active, inactive
                $table->timestamps();
            });
        }

        // 2. School Classes & Sections
        if (!Schema::hasTable('school_classes')) {
            Schema::create('school_classes', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // e.g. "7A", "8B"
                $table->string('grade'); // e.g. "7", "8"
                $table->string('section'); // e.g. "A", "B"
                $table->string('room_number')->nullable();
                $table->integer('capacity')->default(40);
                $table->unsignedBigInteger('teacher_id')->nullable();
                $table->unsignedBigInteger('academic_year_id')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 3. Course Subjects Catalog (for school curriculum)
        if (!Schema::hasTable('course_subjects')) {
            Schema::create('course_subjects', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique(); // e.g. "KHM-01", "MTH-01"
                $table->string('name'); // e.g. "ភាសាខ្មែរ", "គណិតវិទ្យា"
                $table->text('description')->nullable();
                $table->integer('credit_hours')->default(2);
                $table->string('grade_level')->nullable(); // e.g. "7,8,9" or "all"
                $table->timestamps();
            });
        }

        // 4. Update Students table safely
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'student_id_card')) {
                $table->string('student_id_card')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('students', 'status')) {
                $table->string('status')->default('active')->after('grade'); // active, inactive, graduated, transferred, suspended
            }
            if (!Schema::hasColumn('students', 'photo')) {
                $table->string('photo')->nullable()->after('status');
            }
            if (!Schema::hasColumn('students', 'academic_year_id')) {
                $table->unsignedBigInteger('academic_year_id')->nullable()->after('photo');
            }
            if (!Schema::hasColumn('students', 'enrollment_date')) {
                $table->date('enrollment_date')->nullable()->after('academic_year_id');
            }
            if (!Schema::hasColumn('students', 'emergency_contact')) {
                $table->string('emergency_contact')->nullable()->after('enrollment_date');
            }
        });

        // 5. Update Teachers table safely
        Schema::table('teachers', function (Blueprint $table) {
            if (!Schema::hasColumn('teachers', 'teacher_id_card')) {
                $table->string('teacher_id_card')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('teachers', 'photo')) {
                $table->string('photo')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('teachers', 'qualification')) {
                $table->string('qualification')->nullable()->after('subject');
            }
            if (!Schema::hasColumn('teachers', 'specialization')) {
                $table->string('specialization')->nullable()->after('qualification');
            }
            if (!Schema::hasColumn('teachers', 'joining_date')) {
                $table->date('joining_date')->nullable()->after('specialization');
            }
            if (!Schema::hasColumn('teachers', 'salary')) {
                $table->decimal('salary', 10, 2)->default(0.00)->after('joining_date');
            }
            if (!Schema::hasColumn('teachers', 'status')) {
                $table->string('status')->default('active')->after('salary');
            }
        });

        // 6. Parents & Guardians
        if (!Schema::hasTable('parents')) {
            Schema::create('parents', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('name');
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('occupation')->nullable();
                $table->string('address')->nullable();
                $table->string('relationship')->default('អាណាព្យាបាល'); // ឪពុក, ម្តាយ, អាណាព្យាបាល
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('parent_student')) {
            Schema::create('parent_student', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('parent_id');
                $table->unsignedBigInteger('student_id');
                $table->string('relationship')->default('អាណាព្យាបាល');
                $table->timestamps();

                $table->unique(['parent_id', 'student_id']);
            });
        }

        // 7. Timetable Management
        if (!Schema::hasTable('timetables')) {
            Schema::create('timetables', function (Blueprint $table) {
                $table->id();
                $table->string('class_name'); // e.g. "7A"
                $table->unsignedBigInteger('course_subject_id')->nullable();
                $table->unsignedBigInteger('teacher_id')->nullable();
                $table->string('day_of_week'); // ច័ន្ទ, អង្គារ, ពុធ, ព្រហស្បតិ៍, សុក្រ, សៅរ៍
                $table->time('start_time');
                $table->time('end_time');
                $table->string('room_number')->nullable();
                $table->unsignedBigInteger('academic_year_id')->nullable();
                $table->timestamps();
            });
        }

        // 8. Attendance Management
        if (!Schema::hasTable('attendances')) {
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->string('class_name');
                $table->date('date');
                $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present');
                $table->text('remark')->nullable();
                $table->unsignedBigInteger('academic_year_id')->nullable();
                $table->timestamps();

                $table->unique(['student_id', 'date']);
            });
        }

        if (!Schema::hasTable('teacher_attendances')) {
            Schema::create('teacher_attendances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('teacher_id');
                $table->date('date');
                $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present');
                $table->text('remark')->nullable();
                $table->timestamps();

                $table->unique(['teacher_id', 'date']);
            });
        }

        // 9. Examinations & Results
        if (!Schema::hasTable('examinations')) {
            Schema::create('examinations', function (Blueprint $table) {
                $table->id();
                $table->string('title'); // e.g. "ការប្រឡងឆមាសទី ១"
                $table->string('exam_type')->default('semester'); // monthly, midterm, semester, final
                $table->unsignedBigInteger('academic_year_id')->nullable();
                $table->integer('semester')->default(1);
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->boolean('is_published')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('examination_scores')) {
            Schema::create('examination_scores', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('examination_id');
                $table->unsignedBigInteger('student_id');
                $table->string('class_name');
                $table->unsignedBigInteger('course_subject_id')->nullable();
                $table->string('subject_name')->nullable();
                $table->decimal('score', 6, 2)->default(0);
                $table->decimal('max_score', 6, 2)->default(100);
                $table->string('grade_letter')->nullable(); // A, B, C, D, E, F
                $table->timestamps();
            });
        }

        // 10. Finance & Payments
        if (!Schema::hasTable('fee_categories')) {
            Schema::create('fee_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // e.g. "ថ្លៃសិក្សាប្រចាំខែ", "ថ្លៃចុះឈ្មោះ"
                $table->decimal('default_amount', 10, 2)->default(0);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number')->unique();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('fee_category_id')->nullable();
                $table->string('title');
                $table->decimal('total_amount', 10, 2);
                $table->decimal('paid_amount', 10, 2)->default(0);
                $table->date('due_date')->nullable();
                $table->string('status')->default('unpaid'); // unpaid, partial, paid
                $table->string('currency')->default('USD');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('invoice_id');
                $table->string('receipt_number')->unique();
                $table->decimal('amount', 10, 2);
                $table->string('payment_method')->default('cash'); // cash, bank_transfer, qr_code, other
                $table->date('payment_date');
                $table->text('note')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('school_expenses')) {
            Schema::create('school_expenses', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('category')->default('ទូទៅ'); // ប្រាក់ខែ, សម្ភារៈ, ទឹកភ្លើង, ជួសជុល, ផ្សេងៗ
                $table->decimal('amount', 10, 2);
                $table->date('expense_date');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 11. Library Management
        if (!Schema::hasTable('book_categories')) {
            Schema::create('book_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('books')) {
            Schema::create('books', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('book_category_id')->nullable();
                $table->string('isbn')->nullable();
                $table->string('title');
                $table->string('author');
                $table->string('publisher')->nullable();
                $table->integer('quantity')->default(1);
                $table->integer('available_copies')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('book_borrows')) {
            Schema::create('book_borrows', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('book_id');
                $table->unsignedBigInteger('student_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->date('borrow_date');
                $table->date('due_date');
                $table->date('return_date')->nullable();
                $table->string('status')->default('borrowed'); // borrowed, returned, overdue
                $table->timestamps();
            });
        }

        // 12. Announcements & Notifications
        if (!Schema::hasTable('announcements')) {
            Schema::create('announcements', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('content');
                $table->string('category')->default('ទូទៅ'); // ដំណឹង, ព្រឹត្តិការណ៍, ការប្រឡង, បន្ទាន់
                $table->string('target_audience')->default('all'); // all, teachers, students, parents
                $table->date('publish_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 13. System Settings
        if (!Schema::hasTable('system_settings')) {
            Schema::create('system_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('book_borrows');
        Schema::dropIfExists('books');
        Schema::dropIfExists('book_categories');
        Schema::dropIfExists('school_expenses');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('fee_categories');
        Schema::dropIfExists('examination_scores');
        Schema::dropIfExists('examinations');
        Schema::dropIfExists('teacher_attendances');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('timetables');
        Schema::dropIfExists('parent_student');
        Schema::dropIfExists('parents');
        Schema::dropIfExists('course_subjects');
        Schema::dropIfExists('school_classes');
        Schema::dropIfExists('academic_years');
    }
};
