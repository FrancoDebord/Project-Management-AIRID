<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pro_Cl_StudyReportInspection extends Model
{
    protected $table   = 'pro_cl_study_report_inspections';
    protected $guarded = [];

    protected $casts = [
        'sections_done'  => 'array',
        'a_is_conforming' => 'boolean',
        'b_is_conforming' => 'boolean',
        'c_is_conforming' => 'boolean',
        'd_is_conforming' => 'boolean',
        'e_is_conforming' => 'boolean',
    ];

    public function inspection()
    {
        return $this->belongsTo(Pro_QaInspection::class, 'inspection_id');
    }
}
