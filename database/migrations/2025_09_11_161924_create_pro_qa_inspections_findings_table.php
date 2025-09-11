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
        Schema::create('pro_qa_inspections_findings', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger("inspection_id");
             $table->unsignedBigInteger("project_id");
             $table->text("finding_text")->nullable();
             $table->text("action_point")->nullable();
             $table->date("deadline_date")->nullable();
             $table->text("deadline_text")->nullable();
             $table->date("meeting_date")->nullable();
             $table->unsignedBigInteger("assigned_to");
               $table->enum("status", ["pending", "complete"])->default("pending");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_qa_inspections_findings');
    }
};
