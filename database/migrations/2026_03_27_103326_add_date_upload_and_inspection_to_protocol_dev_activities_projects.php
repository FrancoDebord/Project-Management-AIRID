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
        Schema::table('pro_protocols_devs_activities_projects', function (Blueprint $table) {
            // Date automatique du jour d'upload du document (tracabilité)
            $table->date('date_upload')->nullable()->after('real_date_performed');
            // Lien vers l'inspection QA créée automatiquement pour ce document
            $table->unsignedBigInteger('qa_inspection_id')->nullable()->after('date_upload');
            $table->foreign('qa_inspection_id')
                  ->references('id')
                  ->on('pro_qa_inspections')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('pro_protocols_devs_activities_projects', function (Blueprint $table) {
            $table->dropForeign(['qa_inspection_id']);
            $table->dropColumn(['date_upload', 'qa_inspection_id']);
        });
    }
};
