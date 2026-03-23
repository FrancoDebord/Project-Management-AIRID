<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_cl_process_inspection', function (Blueprint $table) {
            $table->id();
            $table->integer('inspection_id')->nullable();
            $table->integer('filled_by')->nullable();

            // Section A — Equipment Reception, Installation and Management (24 questions)
            foreach (range(1, 24) as $n) {
                $table->string("a_q{$n}", 10)->nullable();
            }
            $table->text('a_comments')->nullable();

            // Section B — Test Item Reception, Storage and Management (20 questions)
            foreach (range(1, 20) as $n) {
                $table->string("b_q{$n}", 10)->nullable();
            }
            $table->text('b_comments')->nullable();

            // Section C — Test System Request, Production, Supply and Management (31 questions)
            foreach (range(1, 31) as $n) {
                $table->string("c_q{$n}", 10)->nullable();
            }
            $table->text('c_comments')->nullable();

            // Section D — Computerized system Reception, registration, validation and maintenance (25 questions)
            foreach (range(1, 25) as $n) {
                $table->string("d_q{$n}", 10)->nullable();
            }
            $table->text('d_comments')->nullable();

            // Section E — Safety Procedures (11 questions)
            foreach (range(1, 11) as $n) {
                $table->string("e_q{$n}", 10)->nullable();
            }
            $table->text('e_comments')->nullable();

            $table->json('sections_done')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pro_cl_process_inspection');
    }
};
