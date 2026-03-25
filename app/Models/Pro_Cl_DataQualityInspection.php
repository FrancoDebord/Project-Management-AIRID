<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pro_Cl_DataQualityInspection extends Model
{
    protected $table = 'pro_cl_data_quality_inspections';

    protected $guarded = [];

    protected $casts = [
        'sections_done'       => 'array',
        'aspects_inspected'   => 'array',
        'personnel_involved'  => 'array',
        'a_answers'           => 'array',
        'b_answers'           => 'array',
        'c_v1_answers'        => 'array',
        'c_v2_answers'        => 'array',
        'd_v1_answers'        => 'array',
        'd_v2_answers'        => 'array',
        'e_answers'           => 'array',
        'study_start_date'    => 'date',
        'study_end_date'      => 'date',
        'a_date_performed'    => 'date',
        'b_date_performed'    => 'date',
        'c_v1_date_performed' => 'date',
        'c_v2_date_performed' => 'date',
        'd_v1_date_performed' => 'date',
        'd_v2_date_performed' => 'date',
        'e_date_performed'    => 'date',
        'a_is_conforming'     => 'boolean',
        'b_is_conforming'     => 'boolean',
        'c_is_conforming'     => 'boolean',
        'd_is_conforming'     => 'boolean',
        'e_is_conforming'     => 'boolean',
    ];
}
