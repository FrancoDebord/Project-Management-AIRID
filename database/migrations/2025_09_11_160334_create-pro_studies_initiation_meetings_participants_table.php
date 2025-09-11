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
        Schema::create('pro_studies_initiation_meetings_participants', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger("initiation_meeting_id");
             $table->unsignedBigInteger("participant_id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_studies_initiation_meetings_participants');
    }
};
