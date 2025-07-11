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
        Schema::create('pro_projects_phases_activities_owners', function (Blueprint $table) {
            $table->id();
            $table->integer("project_id");
            $table->integer("activity_id");
            $table->integer("task_owner_id");
             $table->date("date_execution_prevue")->nullable();
            $table->date("date_execution_effective")->nullable();
            $table->text("remarks")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_projects_phases_activities_owners');
    }
};
