<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pro_DmPcAssignment extends Model
{
    protected $table = 'pro_dm_pc_assignments';

    protected $fillable = [
        'project_id', 'pc_name', 'pc_serial', 'is_glp',
        'assigned_at', 'returned_at', 'reason_for_return',
        'assigned_by', 'notes',
    ];

    protected $casts = [
        'is_glp'      => 'boolean',
        'assigned_at' => 'date',
        'returned_at' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Pro_Project::class, 'project_id');
    }

    public function assignedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function isActive(): bool
    {
        return is_null($this->returned_at);
    }
}
