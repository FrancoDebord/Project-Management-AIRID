<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_dm_software_validations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('database_id')->nullable(); // FK to pro_dm_databases

            // Checklist fields (matching the physical form)
            $table->string('computer_id')->nullable();       // "Computer ID"
            $table->string('software_name');                 // "Software to validate"
            $table->date('validation_date')->nullable();
            $table->string('validation_done_by')->nullable();
            $table->string('reason_for_validation')->nullable();
            $table->string('current_software_version')->nullable();
            $table->string('operating_system')->nullable();
            $table->string('cpu')->nullable();
            $table->string('ram')->nullable();
            $table->boolean('is_recorded_in_computer')->nullable();
            $table->enum('validation_kit_status', ['complete', 'incomplete'])->nullable();
            $table->string('validation_folder_name')->nullable();
            $table->string('validation_file_name')->nullable();
            $table->string('sop_document_code')->nullable();
            $table->string('sop_section')->nullable();
            $table->string('env_temperature')->nullable();
            $table->string('env_humidity')->nullable();
            $table->string('data_logger_env')->nullable();
            $table->text('details_of_procedure')->nullable(); // numbered steps text
            $table->enum('status', ['draft', 'validated'])->default('draft');
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('pro_projects')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pro_dm_software_validations');
    }
};
