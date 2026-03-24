<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_cl_study_protocol_inspections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inspection_id');
            $table->unsignedBigInteger('filled_by')->nullable();
            $table->json('sections_done')->nullable();

            // ── A. General (20 questions — stored as 'yes'/'no'/'na') ─
            foreach (range(1, 20) as $n) {
                $table->char("a_q{$n}", 3)->nullable();
            }
            $table->text('a_comments')->nullable();

            // ── B. Test system (4 questions) ───────────────────────
            foreach (range(1, 4) as $n) {
                $table->char("b_q{$n}", 3)->nullable();
            }
            $table->text('b_comments')->nullable();

            // ── C. Test, Control & Reference Articles (6 questions) ─
            foreach (range(1, 6) as $n) {
                $table->char("c_q{$n}", 3)->nullable();
            }
            $table->text('c_comments')->nullable();

            // ── D. Equipment (2 questions) ─────────────────────────
            foreach (range(1, 2) as $n) {
                $table->char("d_q{$n}", 3)->nullable();
            }
            $table->text('d_comments')->nullable();

            // ── E. SOPs (4 questions) ──────────────────────────────
            foreach (range(1, 4) as $n) {
                $table->char("e_q{$n}", 3)->nullable();
            }
            $table->text('e_comments')->nullable();

            // ── F. Study Personnel ─────────────────────────────────
            $table->char('f_q1', 3)->nullable();
            foreach (range(1, 15) as $i) {
                $table->char("f_staff_{$i}_result", 3)->nullable();
                $table->string("f_staff_{$i}_level", 80)->nullable();
                $table->string("f_staff_{$i}_remarks", 80)->nullable();
            }
            $table->text('f_comments')->nullable();

            $table->timestamps();

            $table->foreign('inspection_id')
                  ->references('id')->on('pro_qa_inspections')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pro_cl_study_protocol_inspections');
    }
};
