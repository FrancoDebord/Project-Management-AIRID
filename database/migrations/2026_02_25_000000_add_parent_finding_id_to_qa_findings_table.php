<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pro_qa_inspections_findings', 'parent_finding_id')) {
            Schema::table('pro_qa_inspections_findings', function (Blueprint $table) {
                $table->unsignedBigInteger('parent_finding_id')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        Schema::table('pro_qa_inspections_findings', function (Blueprint $table) {
            $table->dropColumn('parent_finding_id');
        });
    }
};
