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
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'grade')) {
                $table->string('grade')->nullable()->index()->after('isbn'); // '7', '8', '9', '10', '11', '12'
            }
            if (!Schema::hasColumn('books', 'course_subject_id')) {
                $table->unsignedBigInteger('course_subject_id')->nullable()->index()->after('grade');
            }
            if (!Schema::hasColumn('books', 'level')) {
                $table->string('level')->default('basic')->after('course_subject_id'); // 'basic', 'advanced', 'general'
            }
            if (!Schema::hasColumn('books', 'edition')) {
                $table->string('edition')->nullable()->after('publisher'); // e.g. 'បោះពុម្ពលើកទី១', 'កំណែទម្រង់ថ្មី'
            }
            if (!Schema::hasColumn('books', 'publication_year')) {
                $table->integer('publication_year')->nullable()->after('edition'); // e.g. 2024, 2025
            }
            if (!Schema::hasColumn('books', 'cover_image')) {
                $table->string('cover_image')->nullable()->after('publication_year');
            }
            if (!Schema::hasColumn('books', 'views_count')) {
                $table->integer('views_count')->default(0)->after('available_copies');
            }
            if (!Schema::hasColumn('books', 'allow_download')) {
                $table->boolean('allow_download')->default(true)->after('views_count');
            }
            if (!Schema::hasColumn('books', 'is_broken_link')) {
                $table->boolean('is_broken_link')->default(false)->after('allow_download');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn([
                'grade',
                'course_subject_id',
                'level',
                'edition',
                'publication_year',
                'cover_image',
                'views_count',
                'allow_download',
                'is_broken_link',
            ]);
        });
    }
};

