<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_qa_review_inspections', function (Blueprint $table) {
            $table->id();
            $table->date('scheduled_date')->nullable();
            $table->date('review_date')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed'])->default('scheduled');
            $table->string('reviewer_name', 200)->nullable();
            $table->date('date_signed')->nullable();
            $table->date('meeting_date')->nullable();
            $table->text('meeting_participants')->nullable();
            $table->text('meeting_notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qa_review_inspections');
    }
};
