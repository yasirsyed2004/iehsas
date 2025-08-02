<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        if (Schema::hasTable('course_lessons')) {
            // Ensure foreign key to course_modules exists
            $foreignKeys = collect(DB::select("
                SELECT CONSTRAINT_NAME 
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'course_lessons' 
                AND COLUMN_NAME = 'course_module_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            "));

            if ($foreignKeys->isEmpty()) {
                Schema::table('course_lessons', function (Blueprint $table) {
                    if (!Schema::hasColumn('course_lessons', 'course_module_id')) {
                        $table->foreignId('course_module_id')->constrained('course_modules')->onDelete('cascade');
                    } else {
                        $table->foreign('course_module_id')->references('id')->on('course_modules')->onDelete('cascade');
                    }
                });
            }
        } else {
            // Create course_lessons table if it doesn't exist
            Schema::create('course_lessons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_module_id')->constrained('course_modules')->onDelete('cascade');
                $table->string('title');
                $table->text('description');
                $table->enum('type', ['video', 'text', 'quiz', 'assignment']);
                $table->text('content')->nullable();
                $table->string('video_url')->nullable();
                $table->string('file_path')->nullable();
                $table->integer('duration_minutes');
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Now add the missing foreign key to course_progress
        if (Schema::hasTable('course_progress') && Schema::hasTable('course_lessons')) {
            // Check if foreign key exists
            $progressForeignKeys = collect(DB::select("
                SELECT CONSTRAINT_NAME 
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'course_progress' 
                AND COLUMN_NAME = 'course_lesson_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            "));

            if ($progressForeignKeys->isEmpty()) {
                Schema::table('course_progress', function (Blueprint $table) {
                    $table->foreign('course_lesson_id')->references('id')->on('course_lessons')->onDelete('cascade');
                });
            }
        }
    }

    public function down(): void
    {
        // Remove foreign keys if we added them
        if (Schema::hasTable('course_progress')) {
            Schema::table('course_progress', function (Blueprint $table) {
                try {
                    $table->dropForeign(['course_lesson_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
            });
        }

        if (Schema::hasTable('course_lessons')) {
            Schema::table('course_lessons', function (Blueprint $table) {
                try {
                    $table->dropForeign(['course_module_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
            });
        }
    }
};
