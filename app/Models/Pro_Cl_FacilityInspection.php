<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pro_Cl_FacilityInspection extends Model
{
    protected $table = 'pro_cl_facility_inspection';

    protected $guarded = [];

    protected $casts = [
        'sections_done' => 'array',
    ];

    public function inspection()
    {
        return $this->belongsTo(Pro_QaInspection::class, 'inspection_id');
    }
}
