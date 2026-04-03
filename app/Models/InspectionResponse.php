<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionResponse extends Model
{
    protected $table = 'pro_inspection_responses';

    protected $fillable = [
        'inspection_id', 'snapshot_id', 'section_code', 'item_number',
        'yes_no_na', 'text_response', 'is_checked', 'date_value',
        'comments', 'is_conforming', 'ca_completed', 'ca_date',
        'corrective_actions', 'extra_data', 'created_by',
    ];

    protected $casts = [
        'is_checked'    => 'boolean',
        'is_conforming' => 'boolean',
        'ca_completed'  => 'boolean',
        'ca_date'       => 'date',
        'date_value'    => 'date',
        'extra_data'    => 'array',
    ];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Pro_QaInspection::class, 'inspection_id');
    }

    public function snapshot(): BelongsTo
    {
        return $this->belongsTo(InspectionSnapshot::class, 'snapshot_id');
    }
}
