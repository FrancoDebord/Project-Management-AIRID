<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_dm_datalogger_validations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('name');                      // Data logger name/model
            $table->string('serial_number')->nullable();
            $table->string('location')->nullable();      // Where it is deployed
            $table->date('validation_date')->nullable();
            $table->string('validated_by')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'validated'])->default('draft');
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('pro_projects')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pro_dm_datalogger_validations');
    }
};
