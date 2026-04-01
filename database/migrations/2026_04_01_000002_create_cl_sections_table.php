<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cl_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('cl_templates')->cascadeOnDelete();
            $table->string('code');             // A, B, III_A, sp-a, dq-c …
            $table->string('letter')->nullable(); // A, B, C …
            $table->string('title');
            $table->string('subtitle')->nullable(); // sous-titre optionnel
            $table->string('display_style')->default('normal'); // normal | subsection | special
            $table->string('form_type')->nullable(); // yes_no_na | dq_standard | dual_verification | study_box | study_personnel
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['template_id', 'code'], 'cl_sections_template_code_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cl_sections');
    }
};
