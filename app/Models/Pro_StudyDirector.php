<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Represents a personnel member designated as Study Director.
 *
 * This is distinct from users.role = 'study_director':
 *   - users.role → system access/permissions
 *   - pro_study_directors → scientific eligibility to lead a GLP/non-GLP study
 *
 * Only personnel with an active record here appear in SD appointment selects.
 */
class Pro_StudyDirector extends Model
{
    protected $table = 'pro_study_directors';

    protected $fillable = [
        'personnel_id',
        'promoted_by',
        'date_promotion',
        'active',
        'notes',
    ];

    protected $casts = [
        'active'         => 'boolean',
        'date_promotion' => 'date',
    ];

    /** The personnel member designated as SD */
    public function personnel(): BelongsTo
    {
        return $this->belongsTo(Pro_Personnel::class, 'personnel_id');
    }

    /** The user who granted the designation */
    public function promotedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'promoted_by');
    }

    /** Scope: only active designations */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
