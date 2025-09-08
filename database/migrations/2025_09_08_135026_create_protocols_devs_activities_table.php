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
        Schema::create('pro_protocols_devs_activities', function (Blueprint $table) {
            $table->id();
            $table->string("nom_activite");
            $table->text("description_activite")->nullable();
            $table->integer("level_activite");
            $table->enum("staff_role_perform",["Study Director","Facility Manager","Quality Assurance","Project Manager","Other"])->nullable();
            $table->enum("alternative_staff_role_perform",["Study Director","Facility Manager","Quality Assurance","Project Manager","Other"])->nullable();
            $table->enum("multipicite",["une_fois","plusieurs_fois"])->default("une_fois");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_protocols_devs_activities');
    }
};
