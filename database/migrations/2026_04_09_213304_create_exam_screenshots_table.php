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
        Schema::create('exam_screenshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entry_test_attempt_id')->constrained('entry_test_attempts')->onDelete('cascade');
            $table->string('file_path');
            $table->integer('file_size')->default(0);
            $table->timestamp('captured_at');
            $table->timestamps();

            $table->index('entry_test_attempt_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_screenshots');
    }
};
