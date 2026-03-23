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
        Schema::table('pro_projects', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->after('updated_at');
            $table->json('archive_checklist')->nullable()->after('archived_at'); // confirmations manuelles
            $table->foreignId('archived_by')->nullable()->constrained('users')->nullOnDelete()->after('archive_checklist');
        });
    }

    public function down(): void
    {
        Schema::table('pro_projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('archived_by');
            $table->dropColumn(['archived_at', 'archive_checklist']);
        });
    }
};
