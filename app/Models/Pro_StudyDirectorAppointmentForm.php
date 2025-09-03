<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pro_StudyDirectorAppointmentForm extends Model
{
    //

    protected $table = "pro_study_director_appointment_forms";
    protected $guarded = [
        'created_at',
        'updated_at'
    ];

    /**
     * Get the studyDirector that owns the Pro_Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function studyDirector(): BelongsTo
    {
        return $this->belongsTo(Pro_Personnel::class, 'study_director', 'id');
    }

    /**
     * Get the projectManager that owns the Pro_Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function projectManager(): BelongsTo
    {
        return $this->belongsTo(Pro_Personnel::class, 'project_manager', 'id');
    }

}
