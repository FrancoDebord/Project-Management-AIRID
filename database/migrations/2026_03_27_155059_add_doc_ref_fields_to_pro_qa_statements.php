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
        Schema::table('pro_qa_statements', function (Blueprint $table) {
            $table->string('doc_ref')->nullable()->default('QA-PR-L-001/09')->after('report_number');
            $table->date('doc_issue_date')->nullable()->after('doc_ref');
            $table->date('doc_next_review')->nullable()->after('doc_issue_date');
        });
    }

    public function down(): void
    {
        Schema::table('pro_qa_statements', function (Blueprint $table) {
            $table->dropColumn(['doc_ref', 'doc_issue_date', 'doc_next_review']);
        });
    }
};
