<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pro_projects', function (Blueprint $table) {
            $table->string('sponsor_name')->nullable()->after('description_project');
            $table->string('sponsor_email')->nullable()->after('sponsor_name');
            $table->string('manufacturer_name')->nullable()->after('sponsor_email');
        });
    }

    public function down(): void
    {
        Schema::table('pro_projects', function (Blueprint $table) {
            $table->dropColumn(['sponsor_name', 'sponsor_email', 'manufacturer_name']);
        });
    }
};
