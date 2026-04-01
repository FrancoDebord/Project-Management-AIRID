<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionSnapshot extends Model
{
    protected $table = 'inspection_question_snapshots';

    protected $fillable = [
        'inspection_id', 'cl_question_id', 'template_code',
        'section_code', 'section_key', 'section_letter', 'section_title',
        'section_subtitle', 'section_display_style', 'section_form_type',
        'section_sort_order', 'url_slug',
        'item_number', 'text', 'response_type', 'sort_order',
        'snapshotted_at',
    ];

    protected $casts = [
        'snapshotted_at' => 'datetime',
    ];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Pro_QaInspection::class, 'inspection_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(ClQuestion::class, 'cl_question_id');
    }

    public function response(): HasMany
    {
        return $this->hasMany(InspectionResponse::class, 'snapshot_id');
    }
}
