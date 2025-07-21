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
        Schema::create('pro_projects_study_phases_completed', function (Blueprint $table) {
            $table->id();
             $table->integer("project_id")->unsigned()->index();
             $table->integer("study_phase_id")->unsigned()->index();
             $table->date("date_start")->nullable();
             $table->date("date_end")->nullable();
             $table->string("evidence1_file")->nullable();
             $table->string("evidence2_file")->nullable();
             $table->date("date_update_start")->nullable();
             $table->date("date_update_end")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_projects_study_phases_completed');
    }
};
