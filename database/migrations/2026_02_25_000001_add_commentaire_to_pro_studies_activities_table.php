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
        if (!Schema::hasColumn('pro_studies_activities', 'commentaire')) {
            Schema::table('pro_studies_activities', function (Blueprint $table) {
                $table->text('commentaire')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pro_studies_activities', function (Blueprint $table) {
            $table->dropColumn('commentaire');
        });
    }
};

