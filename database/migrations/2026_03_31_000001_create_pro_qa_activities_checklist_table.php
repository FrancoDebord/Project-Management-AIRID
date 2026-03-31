<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_qa_activities_checklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('pro_projects')->onDelete('cascade');
            $table->unsignedTinyInteger('item_number'); // 1–20
            $table->date('date_performed')->nullable();
            $table->string('means_of_verification', 500)->nullable();
            $table->boolean('is_checked')->default(false);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'item_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pro_qa_activities_checklists');
    }
};
