<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_document_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('signer_name');           // displayed name
            $table->string('document_type', 60);     // qa_unit_report | qa_findings_response | qa_statement
            $table->unsignedBigInteger('document_id');// inspection_id or finding_id or statement_id
            $table->string('role_in_document', 60);  // study_director | qa_manager | resolver | facility_manager
            $table->text('signature_data');           // base64 PNG drawn on canvas
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('signed_at');
            $table->timestamps();

            $table->index(['document_type', 'document_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_signatures');
    }
};
