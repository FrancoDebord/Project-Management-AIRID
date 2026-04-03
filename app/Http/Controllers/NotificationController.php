<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Notifications page */
    public function index()
    {
        $base = AppNotification::where('user_id', auth()->id());

        $unread = (clone $base)->whereNull('read_at')
            ->orderByDesc('created_at')
            ->paginate(10, ['*'], 'unread_page')
            ->withQueryString();

        $read = (clone $base)->whereNotNull('read_at')
            ->orderByDesc('created_at')
            ->paginate(20, ['*'], 'read_page')
            ->withQueryString();

        return view('notifications.index', compact('unread', 'read'));
    }

    /** Unread count (JSON) */
    public function unreadCount()
    {
        $count = AppNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }

    /** Latest 8 for dropdown (JSON) */
    public function latest()
    {
        $items = AppNotification::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        return response()->json($items);
    }

    /** Mark one as read */
    public function markRead(AppNotification $notification)
    {
        abort_if($notification->user_id !== auth()->id(), 403);
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }

    /** Mark all as read */
    public function markAllRead()
    {
        AppNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
