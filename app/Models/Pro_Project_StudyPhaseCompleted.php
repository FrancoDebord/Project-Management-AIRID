<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pro_Project_StudyPhaseCompleted extends Model
{
    //

    protected $table = 'pro_projects_study_phases_completed';

    protected $guarded = [
        "created_at",
        "updated_at",
    ];
}
