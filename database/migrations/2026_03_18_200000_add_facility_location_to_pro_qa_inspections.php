<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pro_qa_inspections', function (Blueprint $table) {
            // 'cotonou' or 'cove' — only set when type_inspection = 'Facility Inspection'
            $table->string('facility_location', 20)->nullable()->after('inspection_name');
        });
    }

    public function down(): void
    {
        Schema::table('pro_qa_inspections', function (Blueprint $table) {
            $table->dropColumn('facility_location');
        });
    }
};
