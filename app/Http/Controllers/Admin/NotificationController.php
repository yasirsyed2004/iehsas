<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::forAdmin(Auth::guard('admin')->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function getUnreadCount()
    {
        $count = Notification::forAdmin(Auth::guard('admin')->id())
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    public function getRecent()
    {
        $notifications = Notification::forAdmin(Auth::guard('admin')->id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $unreadCount = Notification::forAdmin(Auth::guard('admin')->id())
            ->unread()
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->notifiable_type !== 'admin' ||
            $notification->notifiable_id !== Auth::guard('admin')->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::forAdmin(Auth::guard('admin')->id())
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
    }

    public function destroy(Notification $notification)
    {
        if ($notification->notifiable_type !== 'admin' ||
            $notification->notifiable_id !== Auth::guard('admin')->id()) {
            return back()->with('error', 'Unauthorized');
        }

        $notification->delete();

        return back()->with('success', 'Notification deleted successfully!');
    }

    public function clearAll()
    {
        Notification::forAdmin(Auth::guard('admin')->id())->delete();

        return back()->with('success', 'All notifications cleared successfully!');
    }
}
