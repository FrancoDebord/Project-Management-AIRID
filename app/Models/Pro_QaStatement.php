<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pro_QaStatement extends Model
{
    protected $table = 'pro_qa_statements';

    protected $guarded = ['created_at', 'updated_at'];

    public function project()
    {
        return $this->belongsTo(Pro_Project::class, 'project_id');
    }

    public function qaManager()
    {
        return $this->belongsTo(Pro_Personnel::class, 'qa_manager_id');
    }
}
