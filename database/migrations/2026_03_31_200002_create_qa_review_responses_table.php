<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qa_review_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')
                  ->constrained('qa_review_inspections')
                  ->onDelete('cascade');
            $table->string('section_code', 10);  // I, II, III_A, III_B, III_C, IV
            $table->unsignedTinyInteger('item_number');
            $table->enum('yes_no', ['yes', 'no'])->nullable();
            $table->text('comments')->nullable();
            $table->text('corrective_actions')->nullable();
            $table->boolean('ca_completed')->default(false);
            $table->date('ca_date')->nullable();
            $table->timestamps();
            $table->unique(['inspection_id', 'section_code', 'item_number'], 'qa_rev_resp_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qa_review_responses');
    }
};
