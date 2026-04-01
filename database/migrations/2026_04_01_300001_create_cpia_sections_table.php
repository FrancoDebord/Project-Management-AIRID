<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cpia_sections', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();   // A, B, C … I
            $table->string('letter', 5);            // A, B, C …
            $table->string('title');                // e.g. "Whole Net cutting and washing"
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('cpia_sections'); }
};
