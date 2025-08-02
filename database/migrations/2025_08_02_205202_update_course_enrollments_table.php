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
        if (Schema::hasTable('course_enrollments')) {
            // Ensure foreign keys exist and point to correct tables
            $foreignKeys = collect(DB::select("
                SELECT COLUMN_NAME, REFERENCED_TABLE_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'course_enrollments' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            "));

            // Check if course_id foreign key exists
            $courseKeyExists = $foreignKeys->where('COLUMN_NAME', 'course_id')->isNotEmpty();
            $userKeyExists = $foreignKeys->where('COLUMN_NAME', 'user_id')->isNotEmpty();

            Schema::table('course_enrollments', function (Blueprint $table) use ($courseKeyExists, $userKeyExists) {
                if (!$courseKeyExists && Schema::hasColumn('course_enrollments', 'course_id')) {
                    $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
                }
                if (!$userKeyExists && Schema::hasColumn('course_enrollments', 'user_id')) {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                }
            });
        } else {
            // Create course_enrollments table if it doesn't exist
            Schema::create('course_enrollments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
                $table->foreignId('entry_test_attempt_id')->nullable()->constrained('entry_test_attempts')->onDelete('set null');
                $table->enum('status', ['enrolled', 'active', 'completed', 'dropped'])->default('enrolled');
                $table->timestamp('enrolled_at');
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->decimal('progress_percentage', 5, 2)->default(0);
                $table->timestamps();

                $table->unique(['user_id', 'course_id']);
            });
        }
    }

    public function down(): void
    {
        // Don't drop existing table, just remove foreign keys if we added them
        if (Schema::hasTable('course_enrollments')) {
            Schema::table('course_enrollments', function (Blueprint $table) {
                try {
                    $table->dropForeign(['course_id']);
                    $table->dropForeign(['user_id']);
                } catch (\Exception $e) {
                    // Foreign keys might not exist
                }
            });
        }
    }
};
