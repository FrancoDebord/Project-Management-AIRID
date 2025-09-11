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
        Schema::create('pro_project_critical_phases_identified', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger("project_id");
              $table->unsignedBigInteger("activity_id");
              $table->date("inspection_date")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_project_critical_phases_identified');
    }
};
