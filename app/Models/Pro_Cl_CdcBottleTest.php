<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pro_Cl_CdcBottleTest extends Model
{
    protected $table = 'pro_cl_cdc_bottle_test';

    protected $fillable = [
        'project_id',
        'project_code',
        'inspection_id',
        'q1',
        'q2',
        'q3',
        'q4',
        'q5',
        'q6',
        'q7',
        'q8',
        'q9',
        'q10',
        'comments',
        'filled_by',
        'is_conforming',
    ];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Pro_QaInspection::class, 'inspection_id');
    }
}
