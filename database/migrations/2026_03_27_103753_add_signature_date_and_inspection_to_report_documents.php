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
        Schema::table('pro_report_phase_documents', function (Blueprint $table) {
            // Date de signature (SD ou toutes parties selon le type de document)
            $table->date('signature_date')->nullable()->after('submission_date');
            // Lien vers l'inspection QA créée automatiquement pour ce document
            $table->unsignedBigInteger('qa_inspection_id')->nullable()->after('signature_date');
            $table->foreign('qa_inspection_id')
                  ->references('id')
                  ->on('pro_qa_inspections')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pro_report_phase_documents', function (Blueprint $table) {
            $table->dropForeign(['qa_inspection_id']);
            $table->dropColumn(['signature_date', 'qa_inspection_id']);
        });
    }
};
