<?php
// File: database/migrations/2025_08_02_000003_create_course_progress_table.php
// UPDATED VERSION - Fix foreign key constraints

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
        Schema::create('course_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            
            // Use unsignedBigInteger first, then add foreign key separately
            $table->unsignedBigInteger('course_lesson_id');
            
            $table->boolean('is_completed')->default(false);
            $table->integer('watch_time_seconds')->default(0);
            $table->decimal('completion_percentage', 5, 2)->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('quiz_answers')->nullable(); // For quiz lessons
            $table->decimal('quiz_score', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'course_lesson_id']);
            $table->index(['user_id', 'course_id']);
        });

        // Add foreign key constraint after table creation (if course_lessons table exists)
        if (Schema::hasTable('course_lessons')) {
            Schema::table('course_progress', function (Blueprint $table) {
                $table->foreign('course_lesson_id')->references('id')->on('course_lessons')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_progress');
    }
};