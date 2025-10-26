<?php
// File: database/migrations/2025_10_26_000001_create_student_course_selections_table.php

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
        Schema::create('student_course_selections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->timestamp('selected_at')->useCurrent();
            $table->timestamps();

            // Composite unique index to prevent duplicate selections
            $table->unique(['student_id', 'course_id']);
            
            // Indexes for faster queries
            $table->index('student_id');
            $table->index('course_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_course_selections');
    }
};