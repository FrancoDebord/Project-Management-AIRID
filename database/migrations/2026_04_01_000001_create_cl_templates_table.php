<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_cl_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();          // ex: facility_main, cone_llin
            $table->string('name');                    // Facility Inspection Checklist (Main)
            $table->string('reference_code')->nullable(); // QA-PR-1-001A/06
            $table->string('version')->default('1.0');
            $table->string('category');               // critical_phase | facility | protocol | qa
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cl_templates');
    }
};
