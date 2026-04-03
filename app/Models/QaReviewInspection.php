<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QaReviewInspection extends Model
{
    protected $table = 'pro_qa_review_inspections';

    protected $fillable = [
        'scheduled_date',
        'review_date',
        'status',
        'reviewer_name',
        'date_signed',
        'meeting_date',
        'meeting_participants',
        'meeting_notes',
        'created_by',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'review_date'    => 'date',
        'date_signed'    => 'date',
        'meeting_date'   => 'date',
    ];

    // ── Sections definition ───────────────────────────────────────────

    public static function sections(): array
    {
        return [
            'I' => [
                'title' => 'QA Staff Records & Trainings',
                'items' => [
                    1 => 'Are personnel records of QA staff complete and up to date?',
                    2 => 'Have all QA staff been trained in line with their respective training logs?',
                    3 => 'Are the training logs of each staff up to date?',
                    4 => 'Did staff achieve competence for each training?',
                    5 => 'Where there any delays in staff trainings?',
                ],
            ],
            'II' => [
                'title' => 'QA Manuals & SOPs',
                'items' => [
                    1 => 'Are all QA Manuals and SOPs up to date?',
                    2 => 'Are all QA Manuals and SOPs available to all QA staff?',
                    3 => 'Can QA staff confidently explain the procedures in the manuals and SOPs?',
                    4 => 'Is the Master Schedule regularly maintained by QA and made available to the Facility manager?',
                ],
            ],
            'III_A' => [
                'title'    => 'QA Activities — A. Facility & Process Inspections',
                'subtitle' => 'A- Facility & Process Inspections',
                'items' => [
                    1 => 'Is the QA calendar available and known by all QA staff?',
                    2 => 'Is the inspection calendar respected? If not explain any delays',
                    3 => 'Are there documented reports of each QA inspection?',
                    4 => 'Are reports signed and staff responsible for corrective actions fully informed?',
                    5 => 'Does QA follow-up on corrective actions? Is this documented regularly?',
                ],
            ],
            'III_B' => [
                'title'    => 'QA Activities — B. Study-based Inspections',
                'subtitle' => 'B- Study-based Inspections',
                'items' => [
                    1 => 'Are all GLP protocols inspected and signed by QAM?',
                    2 => 'Are critical phases for GLP studies selected according to procedures of the Facility?',
                    3 => 'Are critical phases performed as planned?',
                    4 => 'Are QA findings of study-based inspections promptly reported to the Study Director & FM?',
                    5 => 'Does QA follow-up on corrective actions from study-based inspections?',
                ],
            ],
            'III_C' => [
                'title'    => 'QA Activities — C. QA and Study Reports',
                'subtitle' => 'C- QA and Study Reports',
                'items' => [
                    1 => 'Has QA included a statement in each GLP report?',
                    2 => 'Does QA follow-up on study deviations and amendments to ensure they are included in final reports of GLP studies?',
                ],
            ],
            'IV' => [
                'title' => 'Continuous Progress of QA',
                'items' => [
                    1 => 'Are external QA trainings up to date?',
                    2 => 'Are assessment scores achieved acceptable?',
                    3 => 'Is there a plan of smooth succession should the QA Manager be unavoidably absent?',
                ],
            ],
        ];
    }

    public static function documentsToVerify(): array
    {
        return [
            'Manuals' => [
                'Quality Assurance Manual QA-MN-1-001',
                'GLP Training Manual QC-MN-1-002',
                'Quality Assurance Training Manual QA-MN-1-002',
            ],
            'SOPs' => [
                'Quality Assurance for GLP studies SOP-QA-501',
                'Non-conforming event management, corrective and preventive actions SOP-QA-502',
                'Preparing for external audit and inspection SOP-QA-503',
                'Quality indicators SOP-QA-504',
                'Organisation and management of Multi-site studies SOP-QA-505',
            ],
            'Inspections reports' => [
                'Facility inspection reports QA-PR-1-008',
                'Process inspection reports QA-PR-1-008',
                'Study-based inspection reports QA-PR-1-008',
            ],
            'Meeting minutes' => [
                'QA-SD Meetings MG-PR-3-004',
                'Management feedback Meetings QA-PR-1-008',
            ],
            'QA Calendar' => [
                'QA Inspection annual calendar',
                'Studies inspection calendar',
            ],
        ];
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'scheduled'   => 'Scheduled',
            'in_progress' => 'In Progress',
            'completed'   => 'Completed',
            default       => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match($this->status) {
            'scheduled'   => '#0d6efd',
            'in_progress' => '#fd7e14',
            'completed'   => '#198754',
            default       => '#6c757d',
        };
    }

    // ── Relationships ─────────────────────────────────────────────────

    public function responses(): HasMany
    {
        return $this->hasMany(QaReviewResponse::class, 'inspection_id');
    }

    public function customItems(): HasMany
    {
        return $this->hasMany(QaReviewCustomItem::class, 'inspection_id')->orderBy('sort_order');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
