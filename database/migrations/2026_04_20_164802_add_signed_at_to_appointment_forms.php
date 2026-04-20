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
        Schema::table('pro_study_director_appointment_forms', function (Blueprint $table) {
            $table->timestamp('sd_signed_at')->nullable()->after('fm_signature');
            $table->timestamp('fm_signed_at')->nullable()->after('sd_signed_at');
        });
    }

    public function down(): void
    {
        Schema::table('pro_study_director_appointment_forms', function (Blueprint $table) {
            $table->dropColumn(['sd_signed_at', 'fm_signed_at']);
        });
    }
};
