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
        Schema::table('pro_projects', function (Blueprint $table) {
            // Date à laquelle tous les documents ont été remis à l'archiviste
            $table->date('archive_submission_date')->nullable()->after('archived_by');
            // Chemin vers la fiche "Archive Deposition Form and Study Checklist"
            $table->string('archive_deposition_form_path')->nullable()->after('archive_submission_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pro_projects', function (Blueprint $table) {
            $table->dropColumn(['archive_submission_date', 'archive_deposition_form_path']);
        });
    }
};
