<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('subjects', 'coefficient')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->decimal('coefficient', 8, 2)->default(1)->after('sports');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('subjects', 'coefficient')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->dropColumn('coefficient');
            });
        }
    }
};
