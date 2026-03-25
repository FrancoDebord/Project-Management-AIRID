<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_cl_data_quality_inspections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inspection_id');

            $table->json('sections_done')->nullable();

            // Header info (stored when filling section A)
            $table->json('aspects_inspected')->nullable();
            $table->date('study_start_date')->nullable();
            $table->date('study_end_date')->nullable();
            $table->string('study_director_name')->nullable();
            $table->string('qa_inspector_phone')->nullable();
            $table->string('qa_inspector_email')->nullable();
            $table->json('personnel_involved')->nullable();

            // Section A – Staff Training
            $table->json('a_answers')->nullable();
            $table->date('a_date_performed')->nullable();
            $table->unsignedBigInteger('a_qa_personnel_id')->nullable();
            $table->text('a_comments')->nullable();
            $table->boolean('a_is_conforming')->nullable();

            // Section B – Computerised Systems and Softwares Validation
            $table->json('b_answers')->nullable();
            $table->date('b_date_performed')->nullable();
            $table->unsignedBigInteger('b_qa_personnel_id')->nullable();
            $table->text('b_comments')->nullable();
            $table->boolean('b_is_conforming')->nullable();

            // Section C – Data Validity (dual verification)
            $table->json('c_v1_answers')->nullable();
            $table->date('c_v1_date_performed')->nullable();
            $table->unsignedBigInteger('c_v1_qa_personnel_id')->nullable();
            $table->json('c_v2_answers')->nullable();
            $table->date('c_v2_date_performed')->nullable();
            $table->unsignedBigInteger('c_v2_qa_personnel_id')->nullable();
            $table->text('c_comments')->nullable();
            $table->boolean('c_is_conforming')->nullable();

            // Section D – Data Sheet Information (dual verification)
            $table->json('d_v1_answers')->nullable();
            $table->date('d_v1_date_performed')->nullable();
            $table->unsignedBigInteger('d_v1_qa_personnel_id')->nullable();
            $table->json('d_v2_answers')->nullable();
            $table->date('d_v2_date_performed')->nullable();
            $table->unsignedBigInteger('d_v2_qa_personnel_id')->nullable();
            $table->text('d_comments')->nullable();
            $table->boolean('d_is_conforming')->nullable();

            // Section E – Study Box
            // Each answer: {response: yes|no|na, signed: yes|no|na}
            $table->json('e_answers')->nullable();
            $table->date('e_date_performed')->nullable();
            $table->unsignedBigInteger('e_qa_personnel_id')->nullable();
            $table->text('e_comments')->nullable();
            $table->boolean('e_is_conforming')->nullable();

            $table->timestamps();

            $table->foreign('inspection_id')
                  ->references('id')
                  ->on('pro_qa_inspections')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pro_cl_data_quality_inspections');
    }
};
