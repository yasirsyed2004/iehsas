<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->string('action'); // created, status_changed, field_updated, comment_added, progress_logged, reviewed, closed, reopened
            $table->string('field_name')->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('performed_by_type'); // 'admin' or 'user'
            $table->unsignedBigInteger('performed_by_id');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['task_id', 'created_at']);
            $table->index(['performed_by_type', 'performed_by_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_audit_logs');
    }
};
