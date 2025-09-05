<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pro_StudyActivities extends Model
{
    //
    protected $table = "pro_studies_activities";
    protected $guarded = ['created_at', 'updated_at'];

    /**
     * Get the category that owns the Pro_StudyActivities
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Pro_StudyTypeSubCategory::class, 'study_sub_category_id', 'id');
    }

    /**
     * Get the personneResponsable that owns the Pro_StudyActivities
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function personneResponsable(): BelongsTo
    {
        return $this->belongsTo(Pro_Personnel::class, 'should_be_performed_by', 'id');
    }

    /**
     * Get the ParentActivity that owns the Pro_StudyActivities
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ParentActivity(): BelongsTo
    {
        return $this->belongsTo(Pro_StudyActivities::class, 'parent_activity_id', 'id');
    }
}
