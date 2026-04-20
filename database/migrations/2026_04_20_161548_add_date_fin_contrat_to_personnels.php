<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds date_fin_contrat to the personnels table.
 *
 * Combined with the existing sous_contrat (boolean) column, this allows:
 *   - Filtering selects to only active contractors (sous_contrat = 1)
 *   - Daily cron to detect expired contracts and notify Study Directors
 *     about unexecuted activities still assigned to that person
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personnels', function (Blueprint $table) {
            $table->date('date_fin_contrat')->nullable()->after('date_prise_service')
                  ->comment('Contract end date — null means open-ended or not set');
        });
    }

    public function down(): void
    {
        Schema::table('personnels', function (Blueprint $table) {
            $table->dropColumn('date_fin_contrat');
        });
    }
};
