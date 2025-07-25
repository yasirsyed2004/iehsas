<?php
// Create this new migration file:
// database/migrations/2025_01_26_000000_make_user_id_nullable_in_entry_test_attempts.php

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
        Schema::table('entry_test_attempts', function (Blueprint $table) {
            // Make user_id nullable to support both authenticated users and guest students
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entry_test_attempts', function (Blueprint $table) {
            // Revert user_id back to non-nullable (be careful with existing data)
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};