<?php

namespace App\Models;

use App\Mail\AppNotificationMail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Mail;

class AppNotification extends Model
{
    protected $table = 'pro_app_notifications';

    protected $fillable = [
        'user_id', 'type', 'title', 'body', 'data', 'url', 'icon', 'read_at',
    ];

    protected $casts = [
        'data'    => 'array',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    // ── Static helper ───────────────────────────────────────────────

    /**
     * Send a notification to one or several users.
     *
     * @param  int|int[]  $userIds
     */
    public static function send(
        int|array $userIds,
        string $type,
        string $title,
        string $body = '',
        string $url = '',
        string $icon = 'bi-bell',
        array $data = []
    ): void {
        foreach ((array) $userIds as $userId) {
            static::create([
                'user_id' => $userId,
                'type'    => $type,
                'title'   => $title,
                'body'    => $body,
                'url'     => $url,
                'icon'    => $icon,
                'data'    => $data ?: null,
            ]);

            // Also send by email if the user has an email address
            try {
                $user = \App\Models\User::find($userId);
                if ($user && $user->email) {
                    $recipientName = $user->name ?? '';
                    Mail::to($user->email)
                        ->queue(new AppNotificationMail($title, $body, $url, $recipientName));
                }
            } catch (\Throwable $e) {
                // Never let email failure break the notification flow
                \Illuminate\Support\Facades\Log::warning('[AppNotification] Email send failed for user '.$userId.': '.$e->getMessage());
            }
        }
    }
}
