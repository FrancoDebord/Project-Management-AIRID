<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Enhances the existing pro_study_directors table with missing columns.
 *
 * The table already had: id, personnel_id (int), date_promotion, timestamps.
 * This migration adds: promoted_by, active, notes — and makes personnel_id unique.
 *
 * pro_study_directors is SEPARATE from users.role = 'study_director':
 *   - users.role → login/access permissions
 *   - pro_study_directors → scientific eligibility to lead a study
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pro_study_directors', function (Blueprint $table) {
            // Add promoted_by if not already present
            if (!Schema::hasColumn('pro_study_directors', 'promoted_by')) {
                $table->unsignedBigInteger('promoted_by')->nullable()->after('personnel_id')
                      ->comment('FK → users.id — who granted the SD designation');
                $table->foreign('promoted_by')->references('id')->on('users')->onDelete('set null');
            }
            // Add active flag
            if (!Schema::hasColumn('pro_study_directors', 'active')) {
                $table->boolean('active')->default(true)->after('promoted_by')
                      ->comment('Whether the designation is currently active');
            }
            // Add notes
            if (!Schema::hasColumn('pro_study_directors', 'notes')) {
                $table->text('notes')->nullable()->after('active')
                      ->comment('Optional notes about the designation');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pro_study_directors', function (Blueprint $table) {
            $table->dropForeign(['promoted_by']);
            $table->dropColumn(['promoted_by', 'active', 'notes']);
        });
    }
};
