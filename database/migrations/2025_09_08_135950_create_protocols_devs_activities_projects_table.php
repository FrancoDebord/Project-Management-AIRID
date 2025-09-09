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
        Schema::create('pro_protocols_devs_activities_projects', function (Blueprint $table) {
            $table->id();
              $table->unsignedBigInteger("project_id");
              $table->unsignedBigInteger("protocol_dev_activity_id");
              $table->date("date_performed")->nullable();
              $table->date("real_date_performed")->nullable();
              $table->date("due_date_performed")->nullable();
              $table->unsignedBigInteger("staff_id_performed")->nullable();
              $table->string("staff_role")->nullable();
              $table->string("document_file_path")->nullable();
              $table->boolean("applicable")->default(true);
              $table->integer("level_activite")->nullable();
              $table->boolean("complete")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_protocols_devs_activities_projects');
    }
};
