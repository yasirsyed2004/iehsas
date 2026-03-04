<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('web')->user();

        $myTasks = Task::where('assigned_to', $user->id);

        $stats = [
            'total' => (clone $myTasks)->count(),
            'not_started' => (clone $myTasks)->where('status', 'not_started')->count(),
            'in_progress' => (clone $myTasks)->where('status', 'in_progress')->count(),
            'completed' => (clone $myTasks)->where('status', 'completed')->count(),
            'submitted_for_review' => (clone $myTasks)->where('status', 'submitted_for_review')->count(),
            'overdue' => (clone $myTasks)->where('deadline', '<', now())
                ->whereNotIn('status', ['completed', 'cancelled', 'closed'])->count(),
        ];

        $recentTasks = Task::where('assigned_to', $user->id)
            ->with('department')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        $upcomingDeadlines = Task::where('assigned_to', $user->id)
            ->whereNotNull('deadline')
            ->where('deadline', '>=', now())
            ->where('deadline', '<=', now()->addDays(7))
            ->whereNotIn('status', ['completed', 'cancelled', 'closed'])
            ->orderBy('deadline', 'asc')
            ->take(5)
            ->get();

        $unreadNotifications = Notification::where('notifiable_type', 'user')
            ->where('notifiable_id', $user->id)
            ->where('is_read', false)
            ->count();

        return view('staff.dashboard.index', compact('stats', 'recentTasks', 'upcomingDeadlines', 'unreadNotifications'));
    }
}
