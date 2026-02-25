<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pro_QaInspection extends Model
{
    protected $table = 'pro_qa_inspections';

    protected $fillable = [
        'qa_inspector_id',
        'project_id',
        'activity_id',
        'checklist_slug',
        'date_scheduled',
        'date_performed',
        'type_inspection',
        'inspection_name',
    ];

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(Pro_Personnel::class, 'qa_inspector_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Pro_Project::class, 'project_id');
    }

    public function findings(): HasMany
    {
        return $this->hasMany(Pro_QaInspectionFinding::class, 'inspection_id');
    }
}
