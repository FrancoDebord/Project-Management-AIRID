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
        Schema::create('pro_studies_activities', function (Blueprint $table) {
            $table->id();
            $table->string('study_activity_name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('parent_activity_id')->nullable();
            $table->unsignedBigInteger('study_sub_category_id');
            $table->unsignedBigInteger('project_id');
            $table->date('estimated_activity_date')->nullable();
            $table->date('estimated_activity_end_date')->nullable();
            $table->date('actual_activity_date')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('should_be_performed_by')->nullable();
            $table->unsignedBigInteger('performed_by')->nullable();
            $table->string('status')->default('pending')->comment('pending, in_progress, completed, delayed, cancelled');
            $table->boolean('phase_critique')->default(false);
            $table->unsignedBigInteger('meeting_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_studies_activities');
    }
};
