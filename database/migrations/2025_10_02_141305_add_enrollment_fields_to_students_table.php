<?php
// File: database/migrations/YYYY_MM_DD_HHMMSS_add_enrollment_fields_to_students_table.php
// This migration adds enrollment fields and converts CNIC to flexible ID system

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Add new registration fields after existing columns
            $table->string('father_name', 255)->after('full_name');
            $table->date('date_of_birth')->nullable()->after('contact_number');
            $table->enum('id_type', ['cnic', 'passport', 'driving_license'])->default('cnic')->after('date_of_birth');
            $table->string('id_number', 50)->after('id_type');
            $table->string('nationality', 100)->default('Pakistan')->after('id_number');
            $table->text('home_address')->nullable()->after('qualification');
            
            // Add future enrollment verification fields (for Phase 2)
            $table->string('enrollment_verification_code', 4)->nullable()->after('is_retake_allowed');
            $table->timestamp('enrollment_code_generated_at')->nullable()->after('enrollment_verification_code');
            $table->boolean('enrollment_code_used')->default(false)->after('enrollment_code_generated_at');
            $table->boolean('enrollment_form_submitted')->default(false)->after('enrollment_code_used');
        });

        // Step 1: Migrate existing CNIC data to new structure
        DB::statement('UPDATE students SET id_number = cnic, id_type = "cnic" WHERE cnic IS NOT NULL');

        // Step 2: Create composite unique index on (id_type, id_number)
        Schema::table('students', function (Blueprint $table) {
            $table->unique(['id_type', 'id_number'], 'students_id_type_number_unique');
            $table->index(['id_type', 'id_number'], 'students_id_lookup');
        });

        // Step 3: Drop old CNIC column and its constraints
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique(['cnic']); // Drop unique constraint
            $table->dropIndex(['cnic']);  // Drop index
            $table->dropColumn('cnic');   // Drop column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back CNIC column
        Schema::table('students', function (Blueprint $table) {
            $table->string('cnic')->nullable()->after('contact_number');
            $table->index('cnic');
        });

        // Migrate data back to CNIC (only for CNIC type records)
        DB::statement('UPDATE students SET cnic = id_number WHERE id_type = "cnic"');

        // Make CNIC unique again and remove new columns
        Schema::table('students', function (Blueprint $table) {
            $table->string('cnic')->unique()->change();
            
            // Drop new indexes
            $table->dropUnique('students_id_type_number_unique');
            $table->dropIndex('students_id_lookup');
            
            // Drop new columns (in reverse order)
            $table->dropColumn([
                'enrollment_form_submitted',
                'enrollment_code_used', 
                'enrollment_code_generated_at',
                'enrollment_verification_code',
                'home_address',
                'nationality',
                'id_number',
                'id_type',
                'date_of_birth',
                'father_name'
            ]);
        });
    }
};