<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pro_QaInspectionFinding extends Model
{
    protected $table = 'pro_qa_inspections_findings';

    protected $fillable = [
        'inspection_id',
        'project_id',
        'finding_text',
        'action_point',
        'deadline_date',
        'deadline_text',
        'meeting_date',
        'assigned_to',
        'status',
        'parent_finding_id',
    ];

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Pro_Personnel::class, 'assigned_to');
    }

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Pro_QaInspection::class, 'inspection_id');
    }

    public function parentFinding(): BelongsTo
    {
        return $this->belongsTo(Pro_QaInspectionFinding::class, 'parent_finding_id');
    }

    public function childFindings(): HasMany
    {
        return $this->hasMany(Pro_QaInspectionFinding::class, 'parent_finding_id');
    }
}
