<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_cl_study_report_inspections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inspection_id')->unique();
            $table->unsignedBigInteger('filled_by')->nullable();
            $table->json('sections_done')->nullable();

            // ── A. General (28 questions) ──────────────────────────
            foreach (range(1, 28) as $n) {
                $table->string("a_q{$n}")->nullable();
            }
            $table->text('a_comments')->nullable();
            $table->boolean('a_is_conforming')->nullable();

            // ── B. Test, Control and Reference Substances (3 questions) ─
            foreach (range(1, 3) as $n) {
                $table->string("b_q{$n}")->nullable();
            }
            $table->text('b_comments')->nullable();
            $table->boolean('b_is_conforming')->nullable();

            // ── C. Test System Description (2 questions) ───────────
            foreach (range(1, 2) as $n) {
                $table->string("c_q{$n}")->nullable();
            }
            $table->text('c_comments')->nullable();
            $table->boolean('c_is_conforming')->nullable();

            // ── D. Data Management and Statistical Analysis (4 questions) ─
            foreach (range(1, 4) as $n) {
                $table->string("d_q{$n}")->nullable();
            }
            $table->text('d_comments')->nullable();
            $table->boolean('d_is_conforming')->nullable();

            // ── E. Quality Assurance (4 questions) ─────────────────
            foreach (range(1, 4) as $n) {
                $table->string("e_q{$n}")->nullable();
            }
            $table->text('e_comments')->nullable();
            $table->boolean('e_is_conforming')->nullable();

            $table->timestamps();

            $table->foreign('inspection_id')
                  ->references('id')->on('pro_qa_inspections')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pro_cl_study_report_inspections');
    }
};
