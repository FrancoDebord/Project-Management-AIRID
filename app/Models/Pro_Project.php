<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pro_Project extends Model
{
    //

    protected $table = "pro_projects";
    protected $guarded = ["created_at", "updated_at"];

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

    /**
     * The keyPersonnelProject that belong to the Pro_Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function keyPersonnelProject(): BelongsToMany
    {
        return $this->belongsToMany(Pro_Personnel::class, 'pro_projects_team', 'project_id', 'staff_id');
    }

    public function studyDirectorAppointmentForm()
    {
        return $this->hasOne(Pro_StudyDirectorAppointmentForm::class, 'project_id', 'id')->where('active', true);
    }
}
