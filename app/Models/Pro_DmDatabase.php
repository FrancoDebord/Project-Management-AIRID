<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pro_DmDatabase extends Model
{
    protected $table = 'pro_dm_databases';

    protected $fillable = [
        'project_id', 'name', 'type', 'lab_test_id', 'description',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Pro_Project::class, 'project_id');
    }

    public function labTest(): BelongsTo
    {
        return $this->belongsTo(Pro_LabTest::class, 'lab_test_id');
    }

    public function softwareValidations(): HasMany
    {
        return $this->hasMany(Pro_DmSoftwareValidation::class, 'database_id');
    }

    public function doubleEntries(): HasMany
    {
        return $this->hasMany(Pro_DmDoubleEntry::class, 'database_id');
    }
}
