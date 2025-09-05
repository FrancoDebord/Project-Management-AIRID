<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pro_StudyType extends Model
{
    //

    protected $table = "pro_studies_types";
    protected $guarded = ['created_at', 'updated_at'];

    /**
     * Get all of the allSubCategories for the Pro_StudyType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allSubCategories(): HasMany
    {
        return $this->hasMany(Pro_StudyTypeSubCategory::class, 'study_type_id', 'id');
    }
}
