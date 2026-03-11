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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');

            // Subjects in Khmer
            $table->integer('math')->nullable();         // គណិតវិទ្យា
            $table->integer('khmer')->nullable();        // ភាសាខ្មែរ
            $table->integer('english')->nullable();      // ភាសាអង់គ្លេស
            $table->integer('history')->nullable();      // ប្រវត្តិវិទ្យា
            $table->integer('geography')->nullable();    // ភូមិវិទ្យា
            $table->integer('chemistry')->nullable();    // គីមីវិទ្យា
            $table->integer('physics')->nullable();      // រូបវិទ្យា
            $table->integer('biology')->nullable();      // ជីវវិទ្យា
            $table->integer('ethics')->nullable();       // សីលធម៌
            $table->integer('sports')->nullable();       // កីឡា

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
