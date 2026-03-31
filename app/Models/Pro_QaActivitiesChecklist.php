<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pro_QaActivitiesChecklist extends Model
{
    protected $table = 'pro_qa_activities_checklists';

    protected $fillable = [
        'project_id',
        'item_number',
        'date_performed',
        'means_of_verification',
        'is_checked',
        'updated_by',
    ];

    protected $casts = [
        'is_checked'     => 'boolean',
        'date_performed' => 'date',
    ];

    /** The 20 fixed activities (label, item number). */
    public static function activities(): array
    {
        return [
            1  => 'Study Protocol received from SD',
            2  => 'Study Protocol inspection performed by QA Manager / Personnel',
            3  => 'Study Protocol Inspection findings reported to Facility Manager and SD',
            4  => 'Study Protocol signed by QA Manager / Personnel',
            5  => 'Copy of approved protocol received from SD',
            6  => 'Critical phase agreement meeting with SD',
            7  => 'Study inspection programme established by QA Manager / Personnel',
            8  => 'Critical phases inspections performed by QA Manager / Personnel',
            9  => 'Data Quality Inspections performed by QA Manager / Personnel',
            10 => 'Copies of Amendment / Deviation forms received from SD',
            11 => 'Amendments / Deviations inspected by QA Manager / Personnel',
            12 => 'Amendments / Deviations inspections findings reported to Facility Manager and SD',
            13 => 'Copy of draft study report received from SD',
            14 => 'Draft study report inspected by QA Manager / Personnel',
            15 => 'Draft study report inspection findings reported to FM and SD',
            16 => 'Copy of final study report received from SD',
            17 => 'Final study report inspected by QA Manager / Personnel',
            18 => 'Final study report inspection findings reported to FM and SD',
            19 => 'QA Statement signed by QA Manager / Personnel',
            20 => 'Archiving of QA file',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Pro_Project::class, 'project_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
