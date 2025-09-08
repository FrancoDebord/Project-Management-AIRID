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
        Schema::create('pro_key_facility_personnels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("personnel_id");
            $table->enum("staff_role",["Facility Manager","Quality Assurance","Archivist"])->nullable();
            $table->unsignedBigInteger("start_date")->nullable();
            $table->unsignedBigInteger("end_date")->nullable();
            $table->boolean("active");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_key_facility_personnels');
    }
};
