<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pro_Personnel extends Model
{
    protected $table = 'personnels';

    protected $casts = [
        'date_fin_contrat'  => 'date',
        'date_prise_service' => 'date',
        'sous_contrat'       => 'boolean',
    ];

    /** The Study Director designation record for this person (if any). */
    public function studyDirectorDesignation(): HasOne
    {
        return $this->hasOne(Pro_StudyDirector::class, 'personnel_id');
    }

    /** Whether this person is currently designated as a Study Director. */
    public function isStudyDirector(): bool
    {
        return $this->studyDirectorDesignation()->where('active', true)->exists();
    }

    /** Whether this person's contract is currently active. */
    public function hasActiveContract(): bool
    {
        if (!$this->sous_contrat) {
            return false;
        }
        // If no end date, treat as open-ended (still active)
        if (!$this->date_fin_contrat) {
            return true;
        }
        return $this->date_fin_contrat->isFuture() || $this->date_fin_contrat->isToday();
    }
}
