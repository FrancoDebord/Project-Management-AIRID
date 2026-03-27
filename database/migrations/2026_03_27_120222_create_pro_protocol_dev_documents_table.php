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
        Schema::create('pro_protocol_dev_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_project_id');
            $table->foreign('activity_project_id')
                  ->references('id')
                  ->on('pro_protocols_devs_activities_projects')
                  ->onDelete('cascade');
            $table->unsignedBigInteger('project_id');
            $table->string('document_file_path')->nullable();
            $table->date('date_performed')->nullable();
            $table->date('date_upload')->nullable();
            $table->unsignedBigInteger('staff_id_performed')->nullable();
            $table->unsignedBigInteger('qa_inspection_id')->nullable();
            $table->foreign('qa_inspection_id')
                  ->references('id')
                  ->on('pro_qa_inspections')
                  ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_protocol_dev_documents');
    }
};
