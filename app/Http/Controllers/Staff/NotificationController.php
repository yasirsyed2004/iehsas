<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::guard('web')->user();

        $notifications = Notification::where('notifiable_type', 'user')
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('staff.notifications.index', compact('notifications'));
    }

    public function getUnreadCount()
    {
        $user = Auth::guard('web')->user();

        $count = Notification::where('notifiable_type', 'user')
            ->where('notifiable_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    public function markAsRead(Notification $notification)
    {
        $user = Auth::guard('web')->user();

        if ($notification->notifiable_type !== 'user' || $notification->notifiable_id !== $user->id) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        $user = Auth::guard('web')->user();

        Notification::where('notifiable_type', 'user')
            ->where('notifiable_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
