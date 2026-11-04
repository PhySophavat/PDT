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
            $table->string('name_khmer')->nullable()->after('user_id');
            $table->string('name_latin')->nullable()->after('name_khmer');
            $table->string('address')->nullable()->after('province');
            $table->string('parent_name')->nullable()->after('high_school');
            $table->string('parent_relationship')->nullable()->after('parent_name');
            $table->string('parent_occupation')->nullable()->after('parent_relationship');
            $table->string('parent_phone')->nullable()->after('parent_occupation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'name_khmer',
                'name_latin',
                'address',
                'parent_name',
                'parent_relationship',
                'parent_occupation',
                'parent_phone',
            ]);
        });
    }
};
