<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pro_cl_study_protocol_inspections', function (Blueprint $table) {
            $table->boolean('is_conforming')->nullable()->default(null)->after('sections_done');
        });

        Schema::table('pro_cl_amendment_deviation_inspections', function (Blueprint $table) {
            $table->boolean('is_conforming')->nullable()->default(null)->after('comments');
        });
    }

    public function down(): void
    {
        Schema::table('pro_cl_study_protocol_inspections', function (Blueprint $table) {
            $table->dropColumn('is_conforming');
        });

        Schema::table('pro_cl_amendment_deviation_inspections', function (Blueprint $table) {
            $table->dropColumn('is_conforming');
        });
    }
};
