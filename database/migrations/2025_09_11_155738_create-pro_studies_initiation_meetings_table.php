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
        Schema::create('pro_studies_initiation_meetings', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger("project_id");
             $table->unsignedBigInteger("organizer_id");
             $table->date("date_scheduled")->nullable();
             $table->date("date_performed")->nullable();
             $table->enum("status",["complete","pending","cancelled"])->default("pending");
             $table->date("study_initiation_meeting_report")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_studies_initiation_meetings');
    }
};
