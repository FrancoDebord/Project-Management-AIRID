<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CpiaItem extends Model
{
    protected $table = 'cpia_items';

    protected $fillable = [
        'section_id', 'item_number', 'text', 'sort_order',
        'is_active', 'copied_from_id', 'usage_count', 'first_used_at',
    ];

    protected $casts = ['first_used_at' => 'datetime'];

    public function section(): BelongsTo
    {
        return $this->belongsTo(CpiaSection::class, 'section_id');
    }

    public function copiedFrom(): BelongsTo
    {
        return $this->belongsTo(CpiaItem::class, 'copied_from_id');
    }

    public function isDeletable(): bool { return $this->usage_count === 0; }
    public function isFullyEditable(): bool { return $this->usage_count === 0; }
}
