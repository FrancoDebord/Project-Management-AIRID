<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'app_settings'                 => 'pro_app_settings',
        'app_notifications'            => 'pro_app_notifications',
        'document_signatures'          => 'pro_document_signatures',
        'qa_review_inspections'        => 'pro_qa_review_inspections',
        'qa_review_responses'          => 'pro_qa_review_responses',
        'qa_review_custom_items'       => 'pro_qa_review_custom_items',
        'cl_templates'                 => 'pro_cl_templates',
        'cl_sections'                  => 'pro_cl_sections',
        'cl_questions'                 => 'pro_cl_questions',
        'inspection_question_snapshots' => 'pro_inspection_question_snapshots',
        'inspection_responses'         => 'pro_inspection_responses',
        'cpia_sections'                => 'pro_cpia_sections',
        'cpia_items'                   => 'pro_cpia_items',
        'cpia_assessments'             => 'pro_cpia_assessments',
        'cpia_responses'               => 'pro_cpia_responses',
    ];

    public function up(): void
    {
        foreach ($this->tables as $old => $new) {
            if (Schema::hasTable($old) && !Schema::hasTable($new)) {
                Schema::rename($old, $new);
            }
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->tables) as $old => $new) {
            if (Schema::hasTable($new) && !Schema::hasTable($old)) {
                Schema::rename($new, $old);
            }
        }
    }
};
