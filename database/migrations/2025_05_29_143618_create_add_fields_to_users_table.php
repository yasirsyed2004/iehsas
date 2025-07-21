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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('student_id')->unique()->nullable()->after('phone');
            $table->date('date_of_birth')->nullable()->after('student_id');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            $table->text('address')->nullable()->after('gender');
            $table->string('profile_image')->nullable()->after('address');
            $table->boolean('is_active')->default(true)->after('profile_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'student_id', 'date_of_birth', 
                'gender', 'address', 'profile_image', 'is_active'
            ]);
        });
    }
};
