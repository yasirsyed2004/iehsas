<?php
// MISSING MIGRATION 1: Update existing course_modules table
// File: database/migrations/2025_08_02_193300_update_course_modules_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The course_modules table exists but may be referencing the OLD courses table
        // We need to ensure it works with the NEW courses table structure
        
        if (Schema::hasTable('course_modules')) {
            // Check if foreign key exists and is correct
            $foreignKeys = collect(DB::select("
                SELECT CONSTRAINT_NAME 
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'course_modules' 
                AND COLUMN_NAME = 'course_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            "));

            // If no proper foreign key, add it
            if ($foreignKeys->isEmpty()) {
                Schema::table('course_modules', function (Blueprint $table) {
                    // Ensure the column exists and add foreign key
                    if (!Schema::hasColumn('course_modules', 'course_id')) {
                        $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
                    } else {
                        // Add foreign key to existing column
                        $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
                    }
                });
            }
        } else {
            // Create course_modules table if it doesn't exist
            Schema::create('course_modules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
                $table->string('title');
                $table->text('description');
                $table->integer('order')->default(0);
                $table->integer('duration_minutes');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Don't drop existing table, just remove foreign key if we added it
        if (Schema::hasTable('course_modules')) {
            Schema::table('course_modules', function (Blueprint $table) {
                try {
                    $table->dropForeign(['course_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
            });
        }
    }
};