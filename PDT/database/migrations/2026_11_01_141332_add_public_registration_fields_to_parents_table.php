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
        Schema::table('parents', function (Blueprint $table) {
            $table->string('parent_id_card')->nullable()->after('id');
            $table->string('name_khmer')->nullable()->after('name');
            $table->string('name_latin')->nullable()->after('name_khmer');
            $table->string('gender')->nullable()->after('name_latin');
            $table->string('photo')->nullable()->after('address');
            $table->string('child_name')->nullable()->after('relationship');
            $table->string('child_grade')->nullable()->after('child_name');
            $table->string('status')->default('active')->after('child_grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->dropColumn([
                'parent_id_card',
                'name_khmer',
                'name_latin',
                'gender',
                'photo',
                'child_name',
                'child_grade',
                'status',
            ]);
        });
    }
};
