<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::create('scores', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')
          ->constrained('students')
          ->onDelete('cascade')
          ->name('fk_scores_student'); // give a unique name
    $table->foreignId('subject_id')
          ->constrained('subjects')
          ->onDelete('cascade')
          ->name('fk_scores_subject'); // unique name
    $table->integer('score')->nullable();
    $table->timestamps();
});

}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
