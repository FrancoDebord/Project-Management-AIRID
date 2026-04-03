<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QaReviewCustomItem extends Model
{
    protected $table = 'pro_qa_review_custom_items';

    protected $fillable = [
        'inspection_id',
        'sort_order',
        'question',
        'yes_no',
        'comments',
        'corrective_actions',
        'ca_completed',
    ];

    protected $casts = [
        'ca_completed' => 'boolean',
    ];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(QaReviewInspection::class, 'inspection_id');
    }
}
