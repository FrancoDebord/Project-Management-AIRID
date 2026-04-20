<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pro_KeyFacilityPersonnel extends Model
{
    protected $table = "pro_key_facility_personnels";

    protected $guarded = ["created_at", "updated_at"];

    /** The personnel member in this facility role */
    public function personnel(): BelongsTo
    {
        return $this->belongsTo(Pro_Personnel::class, 'personnel_id', 'id');
    }
}
