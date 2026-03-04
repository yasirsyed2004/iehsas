<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyActivityReport;
use App\Models\Department;
use App\Models\User;
use App\Models\Notification;
use App\Mail\DarReviewedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class DarController extends Controller
{
    public function index(Request $request)
    {
        $query = DailyActivityReport::with(['user', 'department', 'reviewer'])
            ->orderBy('report_date', 'desc');

        if ($request->filled('user_id')) {
            $query->forUser($request->user_id);
        }

        if ($request->filled('department_id')) {
            $query->forDepartment($request->department_id);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('report_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('report_date', '<=', $request->date_to);
        }

        $dars = $query->paginate(20)->withQueryString();

        $departments = Department::orderBy('name')->get();
        $staffUsers = User::where('role', 'staff')->where('is_active', true)->orderBy('name')->get();

        $stats = [
            'total' => DailyActivityReport::count(),
            'submitted' => DailyActivityReport::byStatus('submitted')->count(),
            'reviewed' => DailyActivityReport::byStatus('reviewed')->count(),
            'revision_requested' => DailyActivityReport::byStatus('revision_requested')->count(),
        ];

        return view('admin.dar.index', compact('dars', 'departments', 'staffUsers', 'stats'));
    }

    public function show(DailyActivityReport $dar)
    {
        $dar->load(['user', 'department', 'entries.relatedTask', 'addendums.addedBy', 'reviewer', 'reportingManager']);

        return view('admin.dar.show', compact('dar'));
    }

    public function review(Request $request, DailyActivityReport $dar)
    {
        if (!$dar->canBeReviewed()) {
            return redirect()->route('admin.dar.show', $dar)
                ->with('error', 'This DAR cannot be reviewed in its current state.');
        }

        $request->validate([
            'action' => 'required|in:approved,revision_requested',
            'reviewer_comment' => 'nullable|string|max:2000',
        ]);

        $admin = Auth::guard('admin')->user();
        $action = $request->action;

        $dar->update([
            'status' => $action === 'approved' ? 'reviewed' : 'revision_requested',
            'reviewed_at' => now(),
            'reviewed_by' => $admin->id,
            'reviewer_comment' => $request->reviewer_comment,
            'is_locked' => $action === 'approved',
        ]);

        // If revision requested, unlock for editing
        if ($action === 'revision_requested') {
            $dar->update(['is_locked' => false]);
        }

        // Notify the employee
        $actionLabel = $action === 'approved' ? 'approved' : 'requested revision on';
        Notification::createForUser(
            $dar->user_id,
            'dar_reviewed',
            'DAR Review',
            $admin->name . ' ' . $actionLabel . ' your DAR for ' . $dar->report_date->format('M d, Y'),
            ['dar_id' => $dar->id]
        );

        try {
            $dar->load(['user', 'reviewer']);
            if ($dar->user->email) {
                Mail::to($dar->user->email)->send(new DarReviewedMail($dar));
            }
        } catch (\Exception $e) {
            // Email failure shouldn't block review
        }

        $message = $action === 'approved'
            ? 'DAR approved successfully.'
            : 'Revision requested. The employee has been notified.';

        return redirect()->route('admin.dar.show', $dar)->with('success', $message);
    }

    public function monthly(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        $departmentId = $request->get('department_id');

        $query = User::where('role', 'staff')->where('is_active', true);
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }
        $staffUsers = $query->orderBy('name')->get();

        // Get all DARs for the month
        $dars = DailyActivityReport::forMonth($year, $month)
            ->when($departmentId, fn($q) => $q->forDepartment($departmentId))
            ->get()
            ->groupBy('user_id');

        // Build calendar data
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        $calendarData = [];

        foreach ($staffUsers as $staff) {
            $userDars = $dars->get($staff->id, collect());
            $days = [];
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = Carbon::create($year, $month, $day);
                $isWeekend = $date->isWeekend();
                $dar = $userDars->firstWhere('report_date', $date->toDateString());

                $days[$day] = [
                    'date' => $date,
                    'is_weekend' => $isWeekend,
                    'dar' => $dar,
                    'status' => $dar ? $dar->status : ($isWeekend ? 'weekend' : ($date->isPast() ? 'missing' : 'future')),
                ];
            }

            $calendarData[] = [
                'user' => $staff,
                'days' => $days,
                'submitted_count' => $userDars->whereIn('status', ['submitted', 'reviewed'])->count(),
                'total_working_days' => collect(range(1, $daysInMonth))->filter(function ($day) use ($year, $month) {
                    return !Carbon::create($year, $month, $day)->isWeekend();
                })->count(),
            ];
        }

        $departments = Department::orderBy('name')->get();

        return view('admin.dar.monthly', compact(
            'calendarData', 'departments', 'year', 'month', 'daysInMonth', 'departmentId'
        ));
    }
}
