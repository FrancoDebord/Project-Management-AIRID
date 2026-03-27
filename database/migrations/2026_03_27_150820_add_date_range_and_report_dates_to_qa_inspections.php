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
        Schema::table('pro_qa_inspections', function (Blueprint $table) {
            // Inspection date range (start / end) — date_scheduled is kept for compat
            $table->date('date_start')->nullable()->after('date_scheduled');
            $table->date('date_end')->nullable()->after('date_start');
            // Dates de rapport
            $table->date('date_report_fm')->nullable()->after('date_end');
            $table->date('date_report_sd')->nullable()->after('date_report_fm');
        });
    }

    public function down(): void
    {
        Schema::table('pro_qa_inspections', function (Blueprint $table) {
            $table->dropColumn(['date_start', 'date_end', 'date_report_fm', 'date_report_sd']);
        });
    }
};
