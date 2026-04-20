<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds meeting-report fields to the study initiation meetings table.
 * Allows uploading a PDF or storing a drafted text report.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pro_studies_initiation_meetings', function (Blueprint $table) {
            $table->string('report_file_path')->nullable()->after('breve_description')
                  ->comment('Path to the uploaded meeting report PDF');
            $table->longText('report_content')->nullable()->after('report_file_path')
                  ->comment('Drafted text content of the meeting report');
            $table->date('report_date')->nullable()->after('report_content')
                  ->comment('Date of the meeting report');
            $table->unsignedBigInteger('report_redacted_by')->nullable()->after('report_date')
                  ->comment('Personnel ID who drafted/uploaded the report');
        });
    }

    public function down(): void
    {
        Schema::table('pro_studies_initiation_meetings', function (Blueprint $table) {
            $table->dropColumn(['report_file_path', 'report_content', 'report_date', 'report_redacted_by']);
        });
    }
};
