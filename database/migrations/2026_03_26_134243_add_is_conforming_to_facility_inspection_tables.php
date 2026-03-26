<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Main facility: sections A–O (15 sections)
        Schema::table('pro_cl_facility_inspection', function (Blueprint $table) {
            foreach (['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o'] as $sec) {
                $table->boolean("{$sec}_is_conforming")->nullable()->after("{$sec}_comments");
            }
        });

        // Covè facility: sections A–I (9 sections)
        Schema::table('pro_cl_facility_inspection_cove', function (Blueprint $table) {
            foreach (['a','b','c','d','e','f','g','h','i'] as $sec) {
                $table->boolean("{$sec}_is_conforming")->nullable()->after("{$sec}_comments");
            }
        });
    }

    public function down(): void
    {
        Schema::table('pro_cl_facility_inspection', function (Blueprint $table) {
            foreach (['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o'] as $sec) {
                $table->dropColumn("{$sec}_is_conforming");
            }
        });

        Schema::table('pro_cl_facility_inspection_cove', function (Blueprint $table) {
            foreach (['a','b','c','d','e','f','g','h','i'] as $sec) {
                $table->dropColumn("{$sec}_is_conforming");
            }
        });
    }
};
