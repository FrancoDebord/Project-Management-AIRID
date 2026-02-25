<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pro_qa_inspections', 'checklist_slug')) {
            Schema::table('pro_qa_inspections', function (Blueprint $table) {
                $table->string('checklist_slug', 60)->nullable()->after('activity_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('pro_qa_inspections', function (Blueprint $table) {
            $table->dropColumn('checklist_slug');
        });
    }
};
