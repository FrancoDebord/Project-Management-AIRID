<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_dm_pcs', function (Blueprint $table) {
            $table->id();
            $table->string('name');                       // identifiant / nom du PC
            $table->string('serial_number')->nullable();  // numéro de série
            $table->string('brand')->nullable();          // marque / modèle
            $table->boolean('is_glp')->default(false);    // PC validé GLP
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pro_dm_pcs');
    }
};
