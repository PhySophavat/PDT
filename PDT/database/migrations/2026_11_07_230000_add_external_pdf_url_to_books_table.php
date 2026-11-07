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
        if (Schema::hasTable('books')) {
            Schema::table('books', function (Blueprint $table) {
                if (!Schema::hasColumn('books', 'external_pdf_url')) {
                    $table->text('external_pdf_url')->nullable()->after('publisher');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('books')) {
            Schema::table('books', function (Blueprint $table) {
                if (Schema::hasColumn('books', 'external_pdf_url')) {
                    $table->dropColumn('external_pdf_url');
                }
            });
        }
    }
};

