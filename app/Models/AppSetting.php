<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    protected $table = 'app_settings';

    protected $fillable = ['key', 'value', 'label', 'description', 'type', 'group', 'sort_order'];

    // ── Static helpers ─────────────────────────────────────────────

    /**
     * Get a setting value by key.
     * Returns $default if the key doesn't exist or value is null.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        return $setting?->value ?? $default;
    }

    /**
     * Set (upsert) a setting value.
     */
    public static function set(string $key, mixed $value): void
    {
        static::where('key', $key)->update(['value' => $value]);
    }

    /**
     * Get all settings as a flat key→value array.
     */
    public static function allAsMap(): array
    {
        return static::all()->pluck('value', 'key')->toArray();
    }

    /**
     * Get settings grouped by group, ordered by sort_order.
     */
    public static function grouped(): \Illuminate\Support\Collection
    {
        return static::orderBy('group')->orderBy('sort_order')->get()->groupBy('group');
    }

    // ── Default settings definition ────────────────────────────────

    public static function defaults(): array
    {
        return [
            // Documents contrôlés
            [
                'key'         => 'doc_issue_date',
                'value'       => null,
                'label'       => 'Issue Date (documents contrôlés)',
                'description' => 'Date d\'émission commune à tous les documents contrôlés générés.',
                'type'        => 'date',
                'group'       => 'documents',
                'sort_order'  => 1,
            ],
            [
                'key'         => 'doc_next_review',
                'value'       => null,
                'label'       => 'Next Review Date (documents contrôlés)',
                'description' => 'Date de prochaine révision commune à tous les documents contrôlés.',
                'type'        => 'date',
                'group'       => 'documents',
                'sort_order'  => 2,
            ],
            // Organisation
            [
                'key'         => 'org_name',
                'value'       => 'CREC-LSHTM',
                'label'       => 'Nom de l\'organisation',
                'description' => null,
                'type'        => 'text',
                'group'       => 'organisation',
                'sort_order'  => 1,
            ],
            [
                'key'         => 'org_address',
                'value'       => null,
                'label'       => 'Adresse',
                'description' => null,
                'type'        => 'text',
                'group'       => 'organisation',
                'sort_order'  => 2,
            ],
            [
                'key'         => 'org_email',
                'value'       => null,
                'label'       => 'Email de contact',
                'description' => null,
                'type'        => 'email',
                'group'       => 'organisation',
                'sort_order'  => 3,
            ],
            [
                'key'         => 'org_phone',
                'value'       => null,
                'label'       => 'Téléphone',
                'description' => null,
                'type'        => 'text',
                'group'       => 'organisation',
                'sort_order'  => 4,
            ],
        ];
    }
}
