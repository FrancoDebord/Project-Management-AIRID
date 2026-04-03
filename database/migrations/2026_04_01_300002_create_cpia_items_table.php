<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_cpia_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('pro_cpia_sections')->cascadeOnDelete();
            $table->unsignedSmallInteger('item_number');
            $table->text('text');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('copied_from_id')->nullable();
            $table->foreign('copied_from_id')->references('id')->on('pro_cpia_items')->nullOnDelete();
            $table->unsignedInteger('usage_count')->default(0);
            $table->timestamp('first_used_at')->nullable();
            $table->timestamps();

            $table->unique(['section_id', 'item_number']);
        });
    }

    public function down(): void { Schema::dropIfExists('cpia_items'); }
};
