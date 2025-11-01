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
        Schema::table('enrollment_documents', function (Blueprint $table) {
            // Add sub_type column after document_type
            $table->string('sub_type', 50)->nullable()->after('document_type');
        });

        // Drop the old unique constraint
        Schema::table('enrollment_documents', function (Blueprint $table) {
            $table->dropUnique(['student_id', 'document_type']);
        });

        // Add new composite unique constraint with sub_type
        Schema::table('enrollment_documents', function (Blueprint $table) {
            $table->unique(['student_id', 'document_type', 'sub_type'], 'student_document_subtype_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollment_documents', function (Blueprint $table) {
            // Drop new constraint
            $table->dropUnique('student_document_subtype_unique');
            
            // Add back old constraint
            $table->unique(['student_id', 'document_type']);
            
            // Drop sub_type column
            $table->dropColumn('sub_type');
        });
    }
};