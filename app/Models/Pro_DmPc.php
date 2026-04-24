<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pro_DmPc extends Model
{
    protected $table = 'pro_dm_pcs';
    protected $guarded = ['created_at', 'updated_at'];

    protected $casts = [
        'is_glp' => 'boolean',
    ];
}
