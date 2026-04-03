<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_cl_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('pro_cl_sections')->cascadeOnDelete();
            $table->string('item_number');   // 1, 2, 2a, 2b … (string for sub-items)
            $table->text('text');
            $table->string('response_type')->default('yes_no_na'); // yes_no_na | yes_no | checkbox_date_text | text_only | staff_training | study_box_item
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('copied_from_id')->nullable(); // traçabilité copie
            $table->text('notes')->nullable();  // note interne admin
            $table->timestamp('first_used_at')->nullable();
            $table->unsignedInteger('usage_count')->default(0);
            $table->timestamps();

            $table->foreign('copied_from_id')->references('id')->on('pro_cl_questions')->nullOnDelete();
            $table->unique(['section_id', 'item_number'], 'cl_questions_section_item_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cl_questions');
    }
};
