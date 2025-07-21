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
        Schema::create('entry_test_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_test_attempt_id')->constrained()->onDelete('cascade');
            $table->foreignId('entry_test_question_id')->constrained()->onDelete('cascade');
            $table->string('selected_answer');
            $table->boolean('is_correct')->default(false);
            $table->integer('marks_obtained')->default(0);
            $table->timestamps();

            // Ensure one answer per question per attempt with custom short name
            $table->unique(['entry_test_attempt_id', 'entry_test_question_id'], 'eta_attempt_question_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entry_test_answers');
    }
};