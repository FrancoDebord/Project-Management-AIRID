<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_dm_software_validation_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('validation_id');
            $table->string('file_path');
            $table->string('original_name');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->foreign('validation_id')
                  ->references('id')->on('pro_dm_software_validations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pro_dm_software_validation_files');
    }
};
