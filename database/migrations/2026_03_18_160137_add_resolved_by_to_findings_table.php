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
            $table->string('resolved_by_name', 255)->nullable()->after('means_of_verification');
        });
    }

    public function down(): void
    {
        Schema::table('pro_qa_inspections_findings', function (Blueprint $table) {
            $table->dropColumn('resolved_by_name');
        });
    }
};
