<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pro_ArchivingDocument extends Model
{
    protected $table = 'pro_archiving_documents';

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'file_path',
        'document_type',
        'physical_location',
        'archive_date',
        'uploaded_by',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Pro_Project::class, 'project_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }
}
