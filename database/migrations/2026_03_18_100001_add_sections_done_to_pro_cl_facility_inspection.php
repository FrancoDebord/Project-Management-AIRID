<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pro_cl_facility_inspection', function (Blueprint $table) {
            $table->json('sections_done')->nullable()->after('inspection_id');
        });
    }

    public function down(): void
    {
        Schema::table('pro_cl_facility_inspection', function (Blueprint $table) {
            $table->dropColumn('sections_done');
        });
    }
};
