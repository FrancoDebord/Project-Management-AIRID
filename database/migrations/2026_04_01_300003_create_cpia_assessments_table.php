<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_cpia_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('pro_projects')->cascadeOnDelete();
            $table->string('project_code', 100)->nullable();
            $table->string('study_director_name', 255)->nullable();
            $table->string('study_title', 500)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->unique('project_id'); // one assessment per project
        });
    }

    public function down(): void { Schema::dropIfExists('cpia_assessments'); }
};
