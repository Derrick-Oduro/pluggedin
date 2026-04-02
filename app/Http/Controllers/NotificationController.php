<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()
            ->notifications()
            ->latest();

        if ($request->string('scope') === 'unread') {
            $query->whereNull('read_at');
        }

        if ($request->filled('type')) {
            $query->where('data->type', $request->string('type'));
        }

        $notifications = $query->paginate(15)->withQueryString();

        $availableTypes = auth()->user()
            ->notifications()
            ->latest()
            ->get()
            ->pluck('data.type')
            ->filter()
            ->unique()
            ->values();

        return view('notifications.index', compact('notifications', 'availableTypes'));
    }

    public function markRead(string $notificationId)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $notificationId)
            ->firstOrFail();

        if (! $notification->read_at) {
            $notification->markAsRead();
        }

        return redirect()->back();
    }

    public function markAllRead(Request $request)
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
