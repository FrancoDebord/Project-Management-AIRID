<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_dm_double_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('database_id')->nullable(); // FK to pro_dm_databases

            $table->date('first_entry_date');
            $table->string('first_entry_by');       // Names (text)
            $table->date('second_entry_date');
            $table->string('second_entry_by');      // Names (text)

            $table->string('comparison_file_path')->nullable();
            $table->string('comparison_file_name')->nullable();

            $table->boolean('is_compliant')->nullable(); // null = not evaluated yet
            $table->text('comments')->nullable();        // if not compliant

            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('pro_projects')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pro_dm_double_entries');
    }
};
