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
        Schema::create('pro_report_phase_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('pro_projects')->cascadeOnDelete();
            $table->string('document_type'); // final_report, scientific_article, publication_link, shared_data, other
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('url')->nullable();
            $table->string('doi')->nullable();
            $table->date('submission_date')->nullable();
            $table->string('status')->default('draft'); // draft, submitted, published
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_report_phase_documents');
    }
};
