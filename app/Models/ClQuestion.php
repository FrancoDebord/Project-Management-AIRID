<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClQuestion extends Model
{
    protected $table = 'pro_cl_questions';

    protected $fillable = [
        'section_id', 'item_number', 'text', 'response_type',
        'sort_order', 'is_active', 'copied_from_id', 'notes',
        'first_used_at', 'usage_count',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'first_used_at' => 'datetime',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(ClSection::class, 'section_id');
    }

    public function copiedFrom(): BelongsTo
    {
        return $this->belongsTo(ClQuestion::class, 'copied_from_id');
    }

    public function copies(): HasMany
    {
        return $this->hasMany(ClQuestion::class, 'copied_from_id');
    }

    /** Whether the question can be deleted (never used in any inspection). */
    public function isDeletable(): bool
    {
        return $this->usage_count === 0;
    }

    /** Whether the question text/type can be fully edited. */
    public function isFullyEditable(): bool
    {
        return $this->usage_count === 0;
    }

    public static function responseTypeLabel(string $type): string
    {
        return match($type) {
            'yes_no_na'          => 'Yes / No / NA',
            'yes_no'             => 'Yes / No',
            'checkbox_date_text' => 'Checkbox + Date + Text',
            'text_only'          => 'Text only',
            'staff_training'     => 'Staff Training (special)',
            'study_box_item'     => 'Study Box (response + signed)',
            default              => $type,
        };
    }
}
