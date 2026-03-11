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
    Schema::table('teachers', function (Blueprint $table) {
        $table->dropForeign(['user_id']); // drop FK constraint first
        $table->dropColumn('user_id');    // then drop the column
    });
}

public function down(): void
{
    Schema::table('teachers', function (Blueprint $table) {
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
    });
}

};
