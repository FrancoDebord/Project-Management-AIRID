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
        Schema::create('activity_execution_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_id');
            $table->unsignedBigInteger('project_id');
            $table->date('execution_date');
            $table->unsignedBigInteger('executed_by');
            $table->text('comments')->nullable();
            $table->string('status')->default('completed')->comment('completed, delayed, failed, partial');
            $table->timestamps();

            $table->foreign('activity_id')->references('id')->on('pro_studies_activities')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('pro_projects')->onDelete('cascade');
            $table->foreign('executed_by')->references('id')->on('personnels')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_execution_logs');
    }
};
