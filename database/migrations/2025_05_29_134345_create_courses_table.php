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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('code')->unique();
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('duration_hours');
            $table->enum('level', ['beginner', 'intermediate', 'advanced']);
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_entry_test')->default(true);
            $table->decimal('min_entry_score', 5, 2)->default(60);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
