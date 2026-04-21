<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pro_DmDataloggerValidation extends Model
{
    protected $table = 'pro_dm_datalogger_validations';

    protected $fillable = [
        'project_id', 'name', 'serial_number', 'location',
        'validation_date', 'validated_by', 'notes', 'status',
    ];

    protected $casts = [
        'validation_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Pro_Project::class, 'project_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(Pro_DmDataloggerFile::class, 'datalogger_validation_id');
    }
}
