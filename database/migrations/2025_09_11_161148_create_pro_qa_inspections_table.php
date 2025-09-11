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
        Schema::create('pro_qa_inspections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("qa_inspector_id")->nullable();
            $table->unsignedBigInteger("project_id")->nullable();
            $table->date("date_scheduled")->nullable();
            $table->date("date_performed")->nullable();
            $table->enum("type_inspection", ["Facility Inspection", "Process Inspection", "Study Inspection","Critical Phase Inspection"])->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_qa_inspections');
    }
};
