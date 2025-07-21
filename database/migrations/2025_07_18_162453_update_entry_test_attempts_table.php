<?php
// File: database/migrations/2025_07_18_162453_update_entry_test_attempts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check if student_id column exists
        $studentIdExists = Schema::hasColumn('entry_test_attempts', 'student_id');
        $expiresAtExists = Schema::hasColumn('entry_test_attempts', 'expires_at');

        // Add student_id column only if it doesn't exist
        if (!$studentIdExists) {
            Schema::table('entry_test_attempts', function (Blueprint $table) {
                $table->foreignId('student_id')->after('user_id')->nullable()->constrained('students')->onDelete('cascade');
            });
        } else {
            // If column exists but foreign key doesn't, add it
            try {
                DB::statement('ALTER TABLE entry_test_attempts ADD CONSTRAINT entry_test_attempts_student_id_foreign FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE');
            } catch (\Exception $e) {
                // Foreign key might already exist, continue
            }
        }

        // Add expires_at column only if it doesn't exist
        if (!$expiresAtExists) {
            Schema::table('entry_test_attempts', function (Blueprint $table) {
                $table->timestamp('expires_at')->nullable()->after('completed_at');
            });
        }

        // Check if old unique constraint exists and drop it
        $indexes = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'entry_test_attempts' 
            AND CONSTRAINT_TYPE = 'UNIQUE'
            AND CONSTRAINT_NAME = 'entry_test_attempts_user_id_entry_test_id_unique'
        ");

        if (count($indexes) > 0) {
            try {
                DB::statement('ALTER TABLE entry_test_attempts DROP INDEX entry_test_attempts_user_id_entry_test_id_unique');
            } catch (\Exception $e) {
                // Index might not exist or already dropped
            }
        }

        // Check if new unique constraint exists, if not add it
        $newIndexes = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'entry_test_attempts' 
            AND CONSTRAINT_TYPE = 'UNIQUE'
            AND CONSTRAINT_NAME = 'student_test_unique'
        ");

        if (count($newIndexes) == 0) {
            try {
                DB::statement('ALTER TABLE entry_test_attempts ADD CONSTRAINT student_test_unique UNIQUE (student_id, entry_test_id)');
            } catch (\Exception $e) {
                // Handle any remaining constraint issues
                echo "Warning: Could not add unique constraint: " . $e->getMessage();
            }
        }
    }

    public function down(): void
    {
        // Drop new unique constraint if it exists
        try {
            DB::statement('ALTER TABLE entry_test_attempts DROP INDEX student_test_unique');
        } catch (\Exception $e) {
            // Constraint might not exist
        }

        // Re-add original unique constraint if it doesn't exist
        $originalIndexes = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'entry_test_attempts' 
            AND CONSTRAINT_TYPE = 'UNIQUE'
            AND CONSTRAINT_NAME = 'entry_test_attempts_user_id_entry_test_id_unique'
        ");

        if (count($originalIndexes) == 0) {
            try {
                DB::statement('ALTER TABLE entry_test_attempts ADD CONSTRAINT entry_test_attempts_user_id_entry_test_id_unique UNIQUE (user_id, entry_test_id)');
            } catch (\Exception $e) {
                // Handle error
            }
        }

        // Remove columns
        Schema::table('entry_test_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('entry_test_attempts', 'student_id')) {
                $table->dropForeign(['student_id']);
                $table->dropColumn('student_id');
            }
            if (Schema::hasColumn('entry_test_attempts', 'expires_at')) {
                $table->dropColumn('expires_at');
            }
        });
    }
};