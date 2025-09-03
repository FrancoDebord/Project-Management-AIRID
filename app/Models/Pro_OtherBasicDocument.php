<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pro_OtherBasicDocument extends Model
{
    //

    protected $table = "pro_other_basic_documents";
    protected $guarded = ["created_at", "updated_at"];

    /**
     * Get the uploadedBy that owns the Pro_OtherBasicDocument
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id');
    }
}
