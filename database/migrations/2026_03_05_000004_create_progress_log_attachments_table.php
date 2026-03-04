<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress_log_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('progress_log_id')->constrained('task_progress_logs')->onDelete('cascade');
            $table->string('original_filename');
            $table->string('stored_filename');
            $table->string('file_path');
            $table->string('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->timestamps();

            $table->index('progress_log_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_log_attachments');
    }
};
