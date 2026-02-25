<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityExecutionLog extends Model
{
    protected $table = 'activity_execution_logs';
    protected $guarded = ['created_at', 'updated_at'];

    /**
     * Get the activity that owns the log
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Pro_StudyActivities::class, 'activity_id', 'id');
    }

    /**
     * Get the project that owns the log
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Pro_Project::class, 'project_id', 'id');
    }

    /**
     * Get the personnel who executed the activity
     */
    public function executedBy(): BelongsTo
    {
        return $this->belongsTo(Pro_Personnel::class, 'executed_by', 'id');
    }
}
