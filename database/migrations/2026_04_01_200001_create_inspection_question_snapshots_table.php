<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_inspection_question_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained('pro_qa_inspections')->cascadeOnDelete();

            // Traceability — nullable because question may be deleted later
            $table->unsignedBigInteger('cl_question_id')->nullable();
            $table->foreign('cl_question_id')->references('id')->on('pro_cl_questions')->nullOnDelete();

            // Template this snapshot belongs to
            $table->string('template_code');        // 'facility_main', 'cone_llin', …

            // Section metadata — frozen at snapshot time
            $table->string('section_code');         // 'a', 'sp-a', 'dq-c', 'main', 'I', …
            $table->string('section_key');          // letter used in field names: 'a','b',… or '' for 'main'
            $table->string('section_letter')->nullable();   // display letter A, B, III_A …
            $table->string('section_title');
            $table->string('section_subtitle')->nullable();
            $table->string('section_display_style')->default('normal');
            $table->string('section_form_type')->nullable(); // yes_no_na | dq_standard | dual_verification | study_box | staff_training | checkbox_date_text
            $table->integer('section_sort_order')->default(0);

            // URL routing slug — e.g. 'facility-a', 'sp-b', 'cone-llin', 'amendment-deviation'
            $table->string('url_slug');

            // Question data — frozen at snapshot time
            $table->string('item_number');          // '1', '2a', 'staff', …
            $table->text('text');
            $table->string('response_type')->default('yes_no_na');
            $table->integer('sort_order')->default(0);

            $table->timestamp('snapshotted_at')->useCurrent();
            $table->timestamps();

            $table->index(['inspection_id', 'url_slug']);
            $table->index(['inspection_id', 'section_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_question_snapshots');
    }
};
