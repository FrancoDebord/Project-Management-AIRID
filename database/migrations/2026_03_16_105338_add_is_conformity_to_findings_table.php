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
        Schema::table('pro_qa_inspections_findings', function (Blueprint $table) {
            $table->boolean('is_conformity')->default(false)->after('finding_text');
        });
    }

    public function down(): void
    {
        Schema::table('pro_qa_inspections_findings', function (Blueprint $table) {
            $table->dropColumn('is_conformity');
        });
    }
};
