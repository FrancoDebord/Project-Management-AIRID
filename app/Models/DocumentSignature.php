<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentSignature extends Model
{
    protected $table = 'pro_document_signatures';

    protected $fillable = [
        'user_id', 'signer_name', 'document_type', 'document_id',
        'role_in_document', 'signature_data', 'ip_address', 'signed_at',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ──────────────────────────────────────────────────────

    public function scopeForDocument($query, string $type, int $id)
    {
        return $query->where('document_type', $type)->where('document_id', $id);
    }

    // ── Static helper ───────────────────────────────────────────────

    public static function getForDocument(string $type, int $id): \Illuminate\Support\Collection
    {
        return static::forDocument($type, $id)->orderBy('signed_at')->get();
    }

    public static function hasRole(string $type, int $id, string $role): bool
    {
        return static::forDocument($type, $id)->where('role_in_document', $role)->exists();
    }
}
