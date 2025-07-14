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
        Schema::create('pro_projects', function (Blueprint $table) {
            $table->id();
            $table->string("project_code");
            $table->string("project_title");
            $table->string("protocol_code");
            $table->integer("study_director")->index();
            $table->date("date_debut_previsionnelle")->nullable();
            $table->date("date_debut_effective")->nullable();
            $table->date("date_fin_previsionnelle")->nullable();
            $table->date("date_fin_effective")->nullable();
            $table->enum("project_stage",["not_started","in progress","suspended","completed"])->default("not_started");
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_projects');
    }
};
