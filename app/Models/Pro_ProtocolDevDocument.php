<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pro_ProtocolDevDocument extends Model
{
    protected $table = 'pro_protocol_dev_documents';

    protected $guarded = ['created_at', 'updated_at'];

    public function activityProject()
    {
        return $this->belongsTo(Pro_ProtocolDevActivityProject::class, 'activity_project_id', 'id');
    }

    public function staffPerformed()
    {
        return $this->belongsTo(Pro_Personnel::class, 'staff_id_performed', 'id');
    }

    public function qaInspection()
    {
        return $this->belongsTo(Pro_QaInspection::class, 'qa_inspection_id', 'id');
    }
}
