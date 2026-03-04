<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dar_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_activity_report_id')->constrained('daily_activity_reports')->cascadeOnDelete();
            $table->time('time_from');
            $table->time('time_to');
            $table->text('activity_description');
            $table->foreignId('related_task_id')->nullable()->constrained('tasks')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dar_entries');
    }
};
