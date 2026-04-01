<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CpiaAssessment extends Model
{
    protected $table = 'cpia_assessments';

    protected $fillable = [
        'project_id', 'project_code', 'study_director_name', 'study_title',
        'status', 'completed_at', 'completed_by',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Pro_Project::class, 'project_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(CpiaResponse::class, 'assessment_id');
    }

    /** Section codes that have at least one filled response */
    public function filledSectionIds(): array
    {
        return $this->responses()
            ->whereNotNull('impact_score')
            ->pluck('section_id')
            ->unique()
            ->values()
            ->toArray();
    }
}
