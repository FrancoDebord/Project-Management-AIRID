<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pro_cl_study_protocol_inspections', function (Blueprint $table) {
            // Remove the single global column added previously
            $table->dropColumn('is_conforming');

            // One conformity flag per section (A–F)
            $table->boolean('a_is_conforming')->nullable()->default(null)->after('a_comments');
            $table->boolean('b_is_conforming')->nullable()->default(null)->after('b_comments');
            $table->boolean('c_is_conforming')->nullable()->default(null)->after('c_comments');
            $table->boolean('d_is_conforming')->nullable()->default(null)->after('d_comments');
            $table->boolean('e_is_conforming')->nullable()->default(null)->after('e_comments');
            $table->boolean('f_is_conforming')->nullable()->default(null)->after('f_comments');
        });
    }

    public function down(): void
    {
        Schema::table('pro_cl_study_protocol_inspections', function (Blueprint $table) {
            $table->dropColumn(['a_is_conforming', 'b_is_conforming', 'c_is_conforming', 'd_is_conforming', 'e_is_conforming', 'f_is_conforming']);
            $table->boolean('is_conforming')->nullable()->default(null);
        });
    }
};
