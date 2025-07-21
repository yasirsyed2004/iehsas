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
        Schema::create('entry_test_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_test_id')->constrained()->onDelete('cascade');
            $table->text('question_text');
            $table->enum('question_type', ['mcq', 'true_false', 'short_answer']);
            $table->json('options'); // For MCQ options
            $table->string('correct_answer');
            $table->integer('marks')->default(5);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entry_test_questions');
    }
};
