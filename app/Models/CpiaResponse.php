<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CpiaResponse extends Model
{
    protected $table = 'cpia_responses';

    protected $fillable = [
        'assessment_id', 'section_id', 'item_id',
        'impact_score', 'is_selected', 'item_text_snapshot', 'created_by',
    ];

    protected $casts = ['is_selected' => 'boolean'];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(CpiaAssessment::class, 'assessment_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CpiaSection::class, 'section_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(CpiaItem::class, 'item_id');
    }
}
