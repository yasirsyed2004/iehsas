<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_activity_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->unsignedBigInteger('reporting_manager_id')->nullable();
            $table->foreign('reporting_manager_id')->references('id')->on('admins')->nullOnDelete();
            $table->date('report_date');
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'revision_requested'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->foreign('reviewed_by')->references('id')->on('admins')->nullOnDelete();
            $table->text('reviewer_comment')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'report_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_activity_reports');
    }
};
