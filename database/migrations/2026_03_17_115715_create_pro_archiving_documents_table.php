<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pro_archiving_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('pro_projects')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('document_type')->default('archive'); // archive, raw_data, correspondence, other
            $table->string('physical_location')->nullable(); // lieu de stockage physique
            $table->date('archive_date')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_archiving_documents');
    }
};
