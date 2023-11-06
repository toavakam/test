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
        Schema::create('attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id');
            $table->string('name');
            $table->string('lastname');
            $table->boolean('completed');
            $table->unsignedSmallInteger('question_count');
            $table->unsignedSmallInteger('correct_answer_count');
            $table->timestamps();

            $table->foreign('test_id')->references('id')->on('tests')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attempts');
    }
};
