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
        Schema::create('pro_qa_statements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('pro_projects')->onDelete('cascade');
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->date('date_signed')->nullable();
            $table->unsignedBigInteger('qa_manager_id')->nullable(); // FK to personnels
            $table->text('intro_text')->nullable();   // editable intro paragraph
            $table->string('report_number')->nullable();  // overridable if needed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_qa_statements');
    }
};
