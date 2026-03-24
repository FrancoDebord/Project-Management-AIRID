<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_cl_amendment_deviation_inspections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inspection_id')->unique();
            $table->unsignedBigInteger('filled_by')->nullable();
            // Header fields specific to this checklist
            $table->string('document_type')->nullable();    // 'Study Protocol' | 'Study Report'
            $table->string('deviation_number')->nullable();
            $table->string('amendment_number')->nullable();
            // 8 YES/NO/NA questions
            $table->string('q1')->nullable();
            $table->string('q2')->nullable();
            $table->string('q3')->nullable();
            $table->string('q4')->nullable();
            $table->string('q5')->nullable();
            $table->string('q6')->nullable();
            $table->string('q7')->nullable();
            $table->string('q8')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pro_cl_amendment_deviation_inspections');
    }
};
