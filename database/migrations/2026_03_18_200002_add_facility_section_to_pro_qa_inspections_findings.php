<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pro_qa_inspections_findings', function (Blueprint $table) {
            // Slug of the facility section e.g. 'facility-a', 'facility-cove-b'
            // NULL for non-facility inspections
            $table->string('facility_section', 50)->nullable()->after('inspection_id');
        });
    }

    public function down(): void
    {
        Schema::table('pro_qa_inspections_findings', function (Blueprint $table) {
            $table->dropColumn('facility_section');
        });
    }
};
