<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pro_Cl_FacilityInspection extends Model
{
    protected $table = 'pro_cl_facility_inspection';

    protected $guarded = [];

    protected $casts = [
        'sections_done'   => 'array',
        'a_is_conforming' => 'boolean',
        'b_is_conforming' => 'boolean',
        'c_is_conforming' => 'boolean',
        'd_is_conforming' => 'boolean',
        'e_is_conforming' => 'boolean',
        'f_is_conforming' => 'boolean',
        'g_is_conforming' => 'boolean',
        'h_is_conforming' => 'boolean',
        'i_is_conforming' => 'boolean',
        'j_is_conforming' => 'boolean',
        'k_is_conforming' => 'boolean',
        'l_is_conforming' => 'boolean',
        'm_is_conforming' => 'boolean',
        'n_is_conforming' => 'boolean',
        'o_is_conforming' => 'boolean',
    ];

    public function inspection()
    {
        return $this->belongsTo(Pro_QaInspection::class, 'inspection_id');
    }
}
