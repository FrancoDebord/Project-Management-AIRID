<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pro_Cl_AmendmentDeviationInspection extends Model
{
    protected $table = 'pro_cl_amendment_deviation_inspections';

    protected $fillable = [
        'inspection_id',
        'filled_by',
        'document_type',
        'deviation_number',
        'amendment_number',
        'q1', 'q2', 'q3', 'q4', 'q5', 'q6', 'q7', 'q8',
        'comments',
    ];

    protected $casts = [
        'filled_by' => 'integer',
    ];
}
