<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pro_qa_inspections', 'inspection_name')) {
            Schema::table('pro_qa_inspections', function (Blueprint $table) {
                $table->string('inspection_name')->nullable()->after('type_inspection');
            });
        }
    }

    public function down(): void
    {
        Schema::table('pro_qa_inspections', function (Blueprint $table) {
            $table->dropColumn('inspection_name');
        });
    }
};
