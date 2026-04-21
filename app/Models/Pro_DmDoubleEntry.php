<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pro_DmDoubleEntry extends Model
{
    protected $table = 'pro_dm_double_entries';

    protected $fillable = [
        'project_id', 'database_id',
        'first_entry_date', 'first_entry_by',
        'second_entry_date', 'second_entry_by',
        'comparison_file_path', 'comparison_file_name',
        'is_compliant', 'comments',
    ];

    protected $casts = [
        'first_entry_date'  => 'date',
        'second_entry_date' => 'date',
        'is_compliant'      => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Pro_Project::class, 'project_id');
    }

    public function database(): BelongsTo
    {
        return $this->belongsTo(Pro_DmDatabase::class, 'database_id');
    }
}
