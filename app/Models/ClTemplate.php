<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ClTemplate extends Model
{
    protected $table = 'cl_templates';

    protected $fillable = [
        'code', 'name', 'reference_code', 'version', 'category', 'description', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function sections(): HasMany
    {
        return $this->hasMany(ClSection::class, 'template_id')->orderBy('sort_order');
    }

    public function questions(): HasManyThrough
    {
        return $this->hasManyThrough(ClQuestion::class, ClSection::class, 'template_id', 'section_id');
    }

    public function activeQuestionsCount(): int
    {
        return $this->questions()->where('cl_questions.is_active', true)->count();
    }

    public function totalUsage(): int
    {
        return $this->questions()->sum('usage_count');
    }

    public static function categoryLabel(string $category): string
    {
        return match($category) {
            'critical_phase' => 'Critical Phase Inspection',
            'facility'       => 'Facility / Process Inspection',
            'protocol'       => 'Study-based Inspection',
            'qa'             => 'QA / Facility Manager',
            default          => ucfirst($category),
        };
    }

    public static function categoryColor(string $category): string
    {
        return match($category) {
            'critical_phase' => '#6f42c1',
            'facility'       => '#0d6efd',
            'protocol'       => '#198754',
            'qa'             => '#c20102',
            default          => '#6c757d',
        };
    }
}
