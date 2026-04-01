<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClSection extends Model
{
    protected $table = 'cl_sections';

    protected $fillable = [
        'template_id', 'code', 'letter', 'title', 'subtitle',
        'display_style', 'form_type', 'sort_order', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function template(): BelongsTo
    {
        return $this->belongsTo(ClTemplate::class, 'template_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ClQuestion::class, 'section_id')->orderBy('sort_order');
    }

    public function activeQuestions(): HasMany
    {
        return $this->hasMany(ClQuestion::class, 'section_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }
}
