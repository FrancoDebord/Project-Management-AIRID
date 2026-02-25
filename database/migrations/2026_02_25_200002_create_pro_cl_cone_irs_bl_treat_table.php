<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_cl_cone_irs_bl_treat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('project_code', 50)->nullable();
            $table->unsignedBigInteger('inspection_id');
            $table->enum('q1', ['yes', 'no', 'na'])->nullable();
            $table->enum('q2', ['yes', 'no', 'na'])->nullable();
            $table->enum('q3', ['yes', 'no', 'na'])->nullable();
            $table->enum('q4', ['yes', 'no', 'na'])->nullable();
            $table->enum('q5', ['yes', 'no', 'na'])->nullable();
            $table->enum('q6', ['yes', 'no', 'na'])->nullable();
            $table->enum('q7', ['yes', 'no', 'na'])->nullable();
            $table->enum('q8', ['yes', 'no', 'na'])->nullable();
            $table->enum('q9', ['yes', 'no', 'na'])->nullable();
            $table->enum('q10', ['yes', 'no', 'na'])->nullable();
            $table->text('comments')->nullable();
            $table->unsignedBigInteger('filled_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pro_cl_cone_irs_bl_treat');
    }
};
