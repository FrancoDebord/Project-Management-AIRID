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
        Schema::create('pro_study_activities', function (Blueprint $table) {
            $table->id();
            $table->string("activity_title");
            $table->integer("phase_id");
            $table->integer("nb_days_min")->default(0)->comment("Le nombre de jour au minimum après le début du projet pour que cette tâche soit accomplie");
            $table->integer("nb_days_max")->default(0)->comment("Le nombre de jour au maximum après le début du projet pour que cette tâche soit accomplie");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_study_activities');
    }
};
