<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pro_projects', function (Blueprint $table) {
            $table->boolean('is_legacy')->default(false)->after('is_glp');

            // Legacy key dates
            $table->date('legacy_sd_appointment_date')->nullable()->after('is_legacy');
            $table->date('legacy_protocol_signed_sd_date')->nullable()->after('legacy_sd_appointment_date');
            $table->date('legacy_protocol_signed_all_date')->nullable()->after('legacy_protocol_signed_sd_date');
            $table->date('legacy_first_experiment_date')->nullable()->after('legacy_protocol_signed_all_date');
            $table->date('legacy_last_experiment_date')->nullable()->after('legacy_first_experiment_date');
            $table->date('legacy_final_report_signed_sd_date')->nullable()->after('legacy_last_experiment_date');
            $table->date('legacy_final_report_signed_all_date')->nullable()->after('legacy_final_report_signed_sd_date');
            $table->date('legacy_archive_submission_date')->nullable()->after('legacy_final_report_signed_all_date');
            $table->date('legacy_master_schedule_review_date')->nullable()->after('legacy_archive_submission_date');
        });
    }

    public function down(): void
    {
        Schema::table('pro_projects', function (Blueprint $table) {
            $table->dropColumn([
                'is_legacy',
                'legacy_sd_appointment_date',
                'legacy_protocol_signed_sd_date',
                'legacy_protocol_signed_all_date',
                'legacy_first_experiment_date',
                'legacy_last_experiment_date',
                'legacy_final_report_signed_sd_date',
                'legacy_final_report_signed_all_date',
                'legacy_archive_submission_date',
                'legacy_master_schedule_review_date',
            ]);
        });
    }
};
