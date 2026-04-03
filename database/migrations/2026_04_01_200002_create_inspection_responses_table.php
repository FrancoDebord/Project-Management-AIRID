<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_inspection_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained('pro_qa_inspections')->cascadeOnDelete();

            // Points to the exact snapshot row this response answers.
            // Nullable for _meta rows which cover a whole section.
            $table->unsignedBigInteger('snapshot_id')->nullable();
            $table->foreign('snapshot_id')->references('id')->on('pro_inspection_question_snapshots')->nullOnDelete();

            $table->string('section_code');

            // '_meta' = section-level row (comments, is_conforming, DQ header, staff table, etc.)
            // All other values = question item_number
            $table->string('item_number');

            // ── Standard response ─────────────────────────────────────────
            $table->enum('yes_no_na', ['yes', 'no', 'na'])->nullable();
            $table->text('text_response')->nullable();   // free-text or textarea
            $table->boolean('is_checked')->nullable();  // for checkbox_date_text
            $table->date('date_value')->nullable();     // for checkbox_date_text

            // ── Section-level fields (only on _meta rows) ─────────────────
            $table->text('comments')->nullable();
            $table->boolean('is_conforming')->nullable();
            $table->boolean('ca_completed')->nullable();
            $table->date('ca_date')->nullable();
            $table->text('corrective_actions')->nullable();

            // ── Flexible bucket for special types ────────────────────────
            // dual_verification: {"v1":"yes","v2":"no"}
            // study_box:         {"response":"yes","signed":"yes"}
            // _meta DQ:          {"date_performed":"..","qa_personnel_id":..,"v1_date":..,"staff":{..}}
            // _meta Amendment:   {"document_type":"..","deviation_number":"..","amendment_number":".."}
            // _meta DQ-A header: {"study_start_date":"..","personnel_involved":[..], ...}
            $table->json('extra_data')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            $table->unique(['inspection_id', 'section_code', 'item_number'], 'insp_resp_unique');
            $table->index(['inspection_id', 'section_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_responses');
    }
};
