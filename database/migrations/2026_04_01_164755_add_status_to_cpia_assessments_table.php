<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cpia_assessments', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('study_title'); // draft | completed
            $table->timestamp('completed_at')->nullable()->after('status');
            $table->unsignedBigInteger('completed_by')->nullable()->after('completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('cpia_assessments', function (Blueprint $table) {
            $table->dropColumn(['status', 'completed_at', 'completed_by']);
        });
    }
};
