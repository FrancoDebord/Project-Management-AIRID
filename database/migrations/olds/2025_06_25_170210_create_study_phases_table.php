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
        Schema::create('pro_study_phases', function (Blueprint $table) {
            $table->id();
            $table->string("phase_title");
             $table->text("description");
            $table->text("evidence1");
            $table->text("evidence2");
            $table->integer("level");
            $table->string("class_couleur");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_study_phases');
    }
};
