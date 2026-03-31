<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qa_review_custom_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')
                  ->constrained('qa_review_inspections')
                  ->onDelete('cascade');
            $table->unsignedTinyInteger('sort_order')->default(1);
            $table->text('question');
            $table->enum('yes_no', ['yes', 'no'])->nullable();
            $table->text('comments')->nullable();
            $table->text('corrective_actions')->nullable();
            $table->boolean('ca_completed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qa_review_custom_items');
    }
};
