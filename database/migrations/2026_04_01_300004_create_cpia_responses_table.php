<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_cpia_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('pro_cpia_assessments')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('pro_cpia_sections')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('pro_cpia_items')->cascadeOnDelete();

            // Impact score out of 10 (null = not filled)
            $table->unsignedTinyInteger('impact_score')->nullable(); // 0-10

            // Whether this phase is selected for inspection
            $table->boolean('is_selected')->default(false);

            // Snapshot of item text at time of filling (immutable)
            $table->text('item_text_snapshot')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->unique(['assessment_id', 'item_id']);
            $table->index(['assessment_id', 'section_id']);
        });
    }

    public function down(): void { Schema::dropIfExists('cpia_responses'); }
};
