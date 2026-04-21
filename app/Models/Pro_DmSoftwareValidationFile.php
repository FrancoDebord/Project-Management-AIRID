<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pro_DmSoftwareValidationFile extends Model
{
    protected $table = 'pro_dm_software_validation_files';

    protected $fillable = [
        'validation_id', 'file_path', 'original_name', 'uploaded_by',
    ];

    public function validation(): BelongsTo
    {
        return $this->belongsTo(Pro_DmSoftwareValidation::class, 'validation_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
