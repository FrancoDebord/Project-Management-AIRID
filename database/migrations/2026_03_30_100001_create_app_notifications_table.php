<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type', 80);          // project_assigned, findings_resolved, signature_requested, etc.
            $table->string('title');
            $table->text('body')->nullable();
            $table->json('data')->nullable();     // extra payload (project_id, inspection_id, …)
            $table->string('url')->nullable();    // redirect link on click
            $table->string('icon', 60)->nullable()->default('bi-bell'); // Bootstrap-icon name
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
    }
};
