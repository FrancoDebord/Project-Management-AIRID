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
        'status',
        'submitted_by',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Pro_Project::class, 'project_id');
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'submitted_by');
    }
}
