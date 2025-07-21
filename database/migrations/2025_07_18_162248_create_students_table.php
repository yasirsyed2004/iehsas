<?php
// File: database/migrations/2025_07_18_000001_create_students_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email');
            $table->string('contact_number');
            $table->string('cnic')->unique(); // Unique CNIC constraint
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('qualification');
            $table->boolean('is_retake_allowed')->default(false);
            $table->timestamps();

            // Indexes
            $table->index('cnic');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};