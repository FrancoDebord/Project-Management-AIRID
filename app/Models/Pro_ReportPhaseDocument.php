<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pro_ReportPhaseDocument extends Model
{
    protected $table = 'pro_report_phase_documents';

    protected $fillable = [
        'project_id',
        'document_type',
        'title',
        'description',
        'file_path',
        'url',
        'doi',
        'submission_date',
        'signature_date',
        'status',
        'submitted_by',
        'qa_inspection_id',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Pro_Project::class, 'project_id');
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'submitted_by');
    }

    public function qaInspection(): BelongsTo
    {
        return $this->belongsTo(Pro_QaInspection::class, 'qa_inspection_id');
    }
}
