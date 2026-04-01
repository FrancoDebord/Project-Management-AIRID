<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CpiaSection extends Model
{
    protected $table = 'cpia_sections';

    protected $fillable = ['code', 'letter', 'title', 'sort_order', 'is_active'];

    public function items(): HasMany
    {
        return $this->hasMany(CpiaItem::class, 'section_id')->orderBy('sort_order');
    }

    public function activeItems(): HasMany
    {
        return $this->hasMany(CpiaItem::class, 'section_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }
}
