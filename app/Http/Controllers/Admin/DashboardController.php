<?php
// File: app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EntryTest;
use App\Models\EntryTestAttempt;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        
        // Enhanced dashboard stats
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 1)->count(),
            'total_students' => Student::count(),
            'total_tests' => EntryTest::count(),
            'active_tests' => EntryTest::where('is_active', true)->count(),
            'total_attempts' => EntryTestAttempt::count(),
            'completed_attempts' => EntryTestAttempt::where('status', 'completed')->count(),
            'passed_attempts' => EntryTestAttempt::completed()->passed()->count(),
            'in_progress_attempts' => EntryTestAttempt::where('status', 'in_progress')->count(),
            'average_score' => round(EntryTestAttempt::completed()->avg('percentage'), 1) ?: 0,
            'today_attempts' => EntryTestAttempt::whereDate('created_at', today())->count(),
            'this_week_attempts' => EntryTestAttempt::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        // Recent activities
        $recentAttempts = EntryTestAttempt::with(['student', 'entryTest'])
            ->latest()
            ->take(10)
            ->get();

        // Chart data for attempts over time (last 7 days)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartData[] = [
                'date' => $date->format('M d'),
                'attempts' => EntryTestAttempt::whereDate('created_at', $date)->count()
            ];
        }

        // Pass rate by test
        $testStats = EntryTest::withCount([
            'attempts',
            'attempts as completed_attempts' => function($query) {
                $query->where('status', 'completed');
            },
            'attempts as passed_attempts' => function($query) {
                $query->completed()->passed();
            }
        ])->get();

        return view('admin.dashboard.index', compact('admin', 'stats', 'recentAttempts', 'chartData', 'testStats'));
    }
}