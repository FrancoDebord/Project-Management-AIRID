<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    public function studyDirectorReplacementHistory()
    {
        return $this->hasOne(Pro_StudyDirectorAppointmentForm::class, 'project_id', 'id')->where('active', false)->orderBy('replacement_date', 'desc') ;
    }

    public function otherBasicDocuments()
    {
        return $this->hasMany(Pro_OtherBasicDocument::class, 'project_id', 'id');
    }

    /**
     * Get all of the allActivities for the Pro_Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allActivitiesProject($study_type_id): HasMany
    {
         $activites = $this->hasMany(Pro_StudyActivities::class, 'project_id', 'id')->where("study_type_id",$study_type_id);


         return $activites;
        
    }
}
