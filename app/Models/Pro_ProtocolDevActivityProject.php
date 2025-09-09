<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pro_ProtocolDevActivityProject extends Model
{
    //
     protected $table="pro_protocols_devs_activities_projects";

    protected $guarded = ["created_at","updated_at"];

    /**
     * Get the protocolDevActivity that owns the Pro_ProtocolDevActivityProject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function protocolDevActivity(): BelongsTo
    {
        return $this->belongsTo(Pro_ProtocolDevActivity::class, 'protocol_dev_activity_id', 'id');
    }
    /**
     * Get the assignedTo that owns the Pro_ProtocolDevActivityProject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Pro_Personnel::class, 'staff_id_assigned', 'id');
    }
    /**
     * Get the assignedTo that owns the Pro_ProtocolDevActivityProject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function staffPerformed(): BelongsTo
    {
        return $this->belongsTo(Pro_Personnel::class, 'staff_id_performed', 'id');
    }
}
