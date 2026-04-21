<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_dm_pc_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('pc_name');          // PC identifier / label
            $table->string('pc_serial')->nullable();
            $table->boolean('is_glp')->default(false);
            $table->date('assigned_at');
            $table->date('returned_at')->nullable();
            $table->string('reason_for_return')->nullable();
            $table->unsignedBigInteger('assigned_by')->nullable(); // user id
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('pro_projects')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pro_dm_pc_assignments');
    }
};
