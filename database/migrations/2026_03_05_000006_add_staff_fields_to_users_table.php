<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Expand role enum to include 'staff'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','teacher','student','staff') DEFAULT 'student'");

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('role')->constrained('departments')->nullOnDelete();
            $table->unsignedBigInteger('reporting_manager_id')->nullable()->after('department_id');

            $table->foreign('reporting_manager_id')->references('id')->on('admins')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['reporting_manager_id']);
            $table->dropForeign(['department_id']);
            $table->dropColumn(['department_id', 'reporting_manager_id']);
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','teacher','student') DEFAULT 'student'");
    }
};
