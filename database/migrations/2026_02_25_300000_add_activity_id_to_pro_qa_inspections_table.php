<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pro_qa_inspections', 'activity_id')) {
            Schema::table('pro_qa_inspections', function (Blueprint $table) {
                $table->unsignedBigInteger('activity_id')->nullable()->after('project_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('pro_qa_inspections', function (Blueprint $table) {
            $table->dropColumn('activity_id');
        });
    }
};
