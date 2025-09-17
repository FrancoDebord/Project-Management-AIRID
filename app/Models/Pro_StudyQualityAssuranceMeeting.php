<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pro_StudyQualityAssuranceMeeting extends Model 
{
    //

    protected $table="pro_studies_initiation_meetings";

    protected $guarded = ["created_at","updated_at"];

    /**
     * The participants that belong to the Pro_StudyQualityAssuranceMeeting
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Pro_Personnel::class, 'pro_studies_initiation_meetings_participants', 'initiation_meeting_id', 'participant_id');
    }
}
