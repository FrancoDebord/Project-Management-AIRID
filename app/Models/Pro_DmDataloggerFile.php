<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pro_DmDataloggerFile extends Model
{
    protected $table = 'pro_dm_datalogger_files';

    protected $fillable = [
        'datalogger_validation_id', 'file_path', 'original_name', 'uploaded_by',
    ];

    public function dataloggerValidation(): BelongsTo
    {
        return $this->belongsTo(Pro_DmDataloggerValidation::class, 'datalogger_validation_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
