<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pro_DmSoftwareValidation extends Model
{
    protected $table = 'pro_dm_software_validations';

    protected $fillable = [
        'project_id', 'database_id', 'computer_id', 'software_name',
        'validation_date', 'validation_done_by', 'reason_for_validation',
        'current_software_version', 'operating_system', 'cpu', 'ram',
        'is_recorded_in_computer', 'validation_kit_status',
        'validation_folder_name', 'validation_file_name',
        'sop_document_code', 'sop_section',
        'env_temperature', 'env_humidity', 'data_logger_env',
        'details_of_procedure', 'status',
    ];

    protected $casts = [
        'validation_date'          => 'date',
        'is_recorded_in_computer'  => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Pro_Project::class, 'project_id');
    }

    public function database(): BelongsTo
    {
        return $this->belongsTo(Pro_DmDatabase::class, 'database_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(Pro_DmSoftwareValidationFile::class, 'validation_id');
    }
}
