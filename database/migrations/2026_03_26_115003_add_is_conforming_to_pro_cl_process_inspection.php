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
        Schema::table('pro_cl_process_inspection', function (Blueprint $table) {
            foreach (['a','b','c','d','e'] as $sec) {
                $table->boolean("{$sec}_is_conforming")->nullable()->after("{$sec}_comments");
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pro_cl_process_inspection', function (Blueprint $table) {
            foreach (['a','b','c','d','e'] as $sec) {
                $table->dropColumn("{$sec}_is_conforming");
            }
        });
    }
};
