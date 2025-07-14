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
        Schema::create('pro_projects_team', function (Blueprint $table) {
            $table->id();
            $table->integer("project_id");
            $table->integer("staff_if");
            $table->string("role");
            $table->date("date_joined");
            $table->date("date_end")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_projects_team');
    }
};
