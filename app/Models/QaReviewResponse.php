<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QaReviewResponse extends Model
{
    protected $table = 'pro_qa_review_responses';

    protected $fillable = [
        'inspection_id',
        'section_code',
        'item_number',
        'yes_no',
        'comments',
        'corrective_actions',
        'ca_completed',
        'ca_date',
    ];

    protected $casts = [
        'ca_completed' => 'boolean',
        'ca_date'      => 'date',
    ];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(QaReviewInspection::class, 'inspection_id');
    }
}
