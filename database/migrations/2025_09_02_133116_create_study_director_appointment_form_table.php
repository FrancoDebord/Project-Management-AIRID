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
        Schema::create('Pro_Study_Director_Appointment_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('study_director');
            $table->string('project_manager')->nullable();
            $table->date('sd_appointment_date')->nullable();
            $table->date('estimated_start_date')->nullable();
            $table->date('estimated_end_date')->nullable();
            $table->string('study_director_signature')->nullable();
            $table->string('quality_assurance_signature')->nullable();
            $table->string('fm_signature')->nullable();
            $table->text('comments')->nullable();
            $table->text('sd_appointment_file')->nullable();
            $table->date('replacement_date')->nullable();
            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Pro_Study_Director_Appointment_forms');
    }
};
