<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Expand status enum to include new statuses
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM(
            'not_started','pending','in_progress','on_hold',
            'completed','submitted_for_review','closed','cancelled'
        ) DEFAULT 'not_started'");

        // Migrate existing 'pending' rows to 'not_started'
        DB::table('tasks')->where('status', 'pending')->update(['status' => 'not_started']);

        // Add new columns
        Schema::table('tasks', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('deadline');
            $table->text('expected_deliverables')->nullable()->after('description');
            $table->timestamp('closed_at')->nullable()->after('completed_at');
            $table->foreignId('closed_by')->nullable()->after('closed_at')
                  ->constrained('admins')->onDelete('set null');
            $table->timestamp('submitted_for_review_at')->nullable()->after('closed_at');
            $table->boolean('is_locked')->default(false)->after('is_external_shared');
        });

        // Remove 'pending' from enum since we replaced it with 'not_started'
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM(
            'not_started','in_progress','on_hold',
            'completed','submitted_for_review','closed','cancelled'
        ) DEFAULT 'not_started'");
    }

    public function down(): void
    {
        // Restore 'pending' status
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM(
            'not_started','pending','in_progress','on_hold',
            'completed','submitted_for_review','closed','cancelled'
        ) DEFAULT 'not_started'");

        DB::table('tasks')->where('status', 'not_started')->update(['status' => 'pending']);

        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM(
            'pending','in_progress','completed','on_hold','cancelled'
        ) DEFAULT 'pending'");

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['closed_by']);
            $table->dropColumn([
                'start_date',
                'expected_deliverables',
                'closed_at',
                'closed_by',
                'submitted_for_review_at',
                'is_locked',
            ]);
        });
    }
};
