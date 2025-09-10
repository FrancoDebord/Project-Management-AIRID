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
        Schema::create('pro_projects_related_lab_tests', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger("project_id");
            $table->unsignedBigInteger("lab_test_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_projects_related_lab_tests');
    }
};
