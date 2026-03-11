<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            $table->date('date_of_birth')->nullable();

            $table->string('gender');
            // $table->string('grade');

            // ទីកន្លែងស្នាក់នៅ
            $table->string('village')->nullable();
            $table->string('commune')->nullable();
            $table->string('district')->nullable();
            $table->string('province')->nullable();

            // ទំនាក់ទំនង
            $table->string('phone')->nullable();
            $table->string('high_school')->nullable();

            // ព័ត៌មានឪពុកម្តាយ
            $table->string('mother_name')->nullable();
            $table->string('mother_phone')->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_phone')->nullable();
            $table->string('grade')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
}