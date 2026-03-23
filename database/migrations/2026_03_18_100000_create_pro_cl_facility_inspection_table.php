<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_cl_facility_inspection', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inspection_id')->index();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('project_code', 100)->nullable();
            $table->unsignedBigInteger('filled_by')->nullable();

            // Section A – Administration (26 questions)
            foreach (range(1, 26) as $n) {
                $table->enum("a_q{$n}", ['yes', 'no', 'na'])->nullable();
            }
            $table->text('a_comments')->nullable();

            // Section B – Document Control (15 questions)
            foreach (range(1, 15) as $n) {
                $table->enum("b_q{$n}", ['yes', 'no', 'na'])->nullable();
            }
            $table->text('b_comments')->nullable();

            // Section C – Bioassay Laboratory (11 questions)
            foreach (range(1, 11) as $n) {
                $table->enum("c_q{$n}", ['yes', 'no', 'na'])->nullable();
            }
            $table->text('c_comments')->nullable();

            // Section D – Biomolecular Room (5 questions)
            foreach (range(1, 5) as $n) {
                $table->enum("d_q{$n}", ['yes', 'no', 'na'])->nullable();
            }
            $table->text('d_comments')->nullable();

            // Section E – Shaker-Bath room and LLIN Washing area (4 questions)
            foreach (range(1, 4) as $n) {
                $table->enum("e_q{$n}", ['yes', 'no', 'na'])->nullable();
            }
            $table->text('e_comments')->nullable();

            // Section F – Chemical & Potter tower Room (19 questions)
            foreach (range(1, 19) as $n) {
                $table->enum("f_q{$n}", ['yes', 'no', 'na'])->nullable();
            }
            $table->text('f_comments')->nullable();

            // Section G – Safety (changing) room (7 questions)
            foreach (range(1, 7) as $n) {
                $table->enum("g_q{$n}", ['yes', 'no', 'na'])->nullable();
            }
            $table->text('g_comments')->nullable();

            // Section H – Storage and untreated block rooms (6 questions)
            foreach (range(1, 6) as $n) {
                $table->enum("h_q{$n}", ['yes', 'no', 'na'])->nullable();
            }
            $table->text('h_comments')->nullable();

            // Section I – Net storage room and expired products Room (4 questions)
            foreach (range(1, 4) as $n) {
                $table->enum("i_q{$n}", ['yes', 'no', 'na'])->nullable();
            }
            $table->text('i_comments')->nullable();

            // Section J – Equipment (15 questions)
            foreach (range(1, 15) as $n) {
                $table->enum("j_q{$n}", ['yes', 'no', 'na'])->nullable();
            }
            $table->text('j_comments')->nullable();

            // Section K – Staff Offices & Buildings (13 questions)
            foreach (range(1, 13) as $n) {
                $table->enum("k_q{$n}", ['yes', 'no', 'na'])->nullable();
            }
            $table->text('k_comments')->nullable();

            // Section L – Data Management (25 questions)
            foreach (range(1, 25) as $n) {
                $table->enum("l_q{$n}", ['yes', 'no', 'na'])->nullable();
            }
            $table->text('l_comments')->nullable();

            // Section M – Archive (16 questions)
            foreach (range(1, 16) as $n) {
                $table->enum("m_q{$n}", ['yes', 'no', 'na'])->nullable();
            }
            $table->text('m_comments')->nullable();

            // Section N – Insectary and Annex (25 questions)
            foreach (range(1, 25) as $n) {
                $table->enum("n_q{$n}", ['yes', 'no', 'na'])->nullable();
            }
            $table->text('n_comments')->nullable();

            // Section O – Animal House (12 questions)
            foreach (range(1, 12) as $n) {
                $table->enum("o_q{$n}", ['yes', 'no', 'na'])->nullable();
            }
            $table->text('o_comments')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pro_cl_facility_inspection');
    }
};
