<?php
// File: database/migrations/2025_08_02_000002_create_courses_table.php

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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->foreignId('category_id')->constrained('course_categories')->onDelete('cascade');
            $table->foreignId('instructor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('thumbnail')->nullable();
            $table->string('banner_image')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced']);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->decimal('price', 8, 2)->default(0);
            $table->decimal('discount_price', 8, 2)->nullable();
            $table->integer('duration_hours')->default(0);
            $table->integer('max_students')->nullable();
            $table->boolean('requires_entry_test')->default(false);
            $table->decimal('min_entry_test_score', 5, 2)->nullable();
            $table->boolean('has_certificate')->default(true);
            $table->json('prerequisites')->nullable(); // Course IDs or requirements
            $table->json('learning_outcomes')->nullable(); // What students will learn
            $table->text('requirements')->nullable(); // What students need before starting
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'is_active']);
            $table->index(['category_id', 'level']);
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};