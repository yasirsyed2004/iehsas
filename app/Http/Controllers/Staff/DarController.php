<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\DailyActivityReport;
use App\Models\DarEntry;
use App\Models\DarAddendum;
use App\Models\Task;
use App\Models\Notification;
use App\Mail\DarSubmittedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DarController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('web')->user();

        $query = DailyActivityReport::forUser($user->id)
            ->with(['department', 'entries'])
            ->orderBy('report_date', 'desc');

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('month')) {
            $parts = explode('-', $request->month);
            if (count($parts) === 2) {
                $query->forMonth($parts[0], $parts[1]);
            }
        }

        $dars = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => DailyActivityReport::forUser($user->id)->count(),
            'draft' => DailyActivityReport::forUser($user->id)->byStatus('draft')->count(),
            'submitted' => DailyActivityReport::forUser($user->id)->byStatus('submitted')->count(),
            'reviewed' => DailyActivityReport::forUser($user->id)->byStatus('reviewed')->count(),
            'revision_requested' => DailyActivityReport::forUser($user->id)->byStatus('revision_requested')->count(),
        ];

        return view('staff.dar.index', compact('dars', 'stats'));
    }

    public function create()
    {
        $user = Auth::guard('web')->user();
        $today = now()->toDateString();

        // Check if DAR already exists for today
        $existingDar = DailyActivityReport::forUser($user->id)->forDate($today)->first();
        if ($existingDar) {
            if ($existingDar->isEditable()) {
                return redirect()->route('staff.dar.edit', $existingDar)
                    ->with('info', 'You already have a DAR for today. You can edit it below.');
            }
            return redirect()->route('staff.dar.show', $existingDar)
                ->with('info', 'Your DAR for today has already been submitted.');
        }

        // Get tasks assigned to user for the dropdown
        $tasks = Task::where('assigned_to', $user->id)
            ->whereNotIn('status', ['closed', 'cancelled'])
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('staff.dar.create', compact('tasks', 'today'));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('web')->user();
        $today = now()->toDateString();

        // Prevent duplicate
        $existing = DailyActivityReport::forUser($user->id)->forDate($today)->first();
        if ($existing) {
            return redirect()->route('staff.dar.show', $existing)
                ->with('error', 'A DAR for today already exists.');
        }

        $request->validate([
            'entries' => 'required|array|min:1',
            'entries.*.time_from' => 'required|date_format:H:i',
            'entries.*.time_to' => 'required|date_format:H:i|after:entries.*.time_from',
            'entries.*.activity_description' => 'required|string|max:1000',
            'entries.*.related_task_id' => 'nullable|exists:tasks,id',
            'entries.*.remarks' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $dar = DailyActivityReport::create([
                'user_id' => $user->id,
                'department_id' => $user->department_id,
                'reporting_manager_id' => $user->reporting_manager_id,
                'report_date' => $today,
                'status' => 'draft',
            ]);

            foreach ($request->entries as $index => $entryData) {
                DarEntry::create([
                    'daily_activity_report_id' => $dar->id,
                    'time_from' => $entryData['time_from'],
                    'time_to' => $entryData['time_to'],
                    'activity_description' => $entryData['activity_description'],
                    'related_task_id' => $entryData['related_task_id'] ?? null,
                    'remarks' => $entryData['remarks'] ?? null,
                    'sort_order' => $index,
                ]);
            }

            DB::commit();
            return redirect()->route('staff.dar.show', $dar)
                ->with('success', 'Daily Activity Report saved as draft.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save DAR. Please try again.')->withInput();
        }
    }

    public function show(DailyActivityReport $dar)
    {
        $user = Auth::guard('web')->user();

        if ($dar->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $dar->load(['entries.relatedTask', 'addendums.addedBy', 'reviewer', 'department']);

        return view('staff.dar.show', compact('dar'));
    }

    public function edit(DailyActivityReport $dar)
    {
        $user = Auth::guard('web')->user();

        if ($dar->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        if (!$dar->isEditable()) {
            return redirect()->route('staff.dar.show', $dar)
                ->with('error', 'This DAR can no longer be edited.');
        }

        $dar->load('entries');

        $tasks = Task::where('assigned_to', $user->id)
            ->whereNotIn('status', ['closed', 'cancelled'])
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('staff.dar.edit', compact('dar', 'tasks'));
    }

    public function update(Request $request, DailyActivityReport $dar)
    {
        $user = Auth::guard('web')->user();

        if ($dar->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        if (!$dar->isEditable()) {
            return redirect()->route('staff.dar.show', $dar)
                ->with('error', 'This DAR can no longer be edited.');
        }

        $request->validate([
            'entries' => 'required|array|min:1',
            'entries.*.time_from' => 'required|date_format:H:i',
            'entries.*.time_to' => 'required|date_format:H:i|after:entries.*.time_from',
            'entries.*.activity_description' => 'required|string|max:1000',
            'entries.*.related_task_id' => 'nullable|exists:tasks,id',
            'entries.*.remarks' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Remove old entries and recreate
            $dar->entries()->delete();

            foreach ($request->entries as $index => $entryData) {
                DarEntry::create([
                    'daily_activity_report_id' => $dar->id,
                    'time_from' => $entryData['time_from'],
                    'time_to' => $entryData['time_to'],
                    'activity_description' => $entryData['activity_description'],
                    'related_task_id' => $entryData['related_task_id'] ?? null,
                    'remarks' => $entryData['remarks'] ?? null,
                    'sort_order' => $index,
                ]);
            }

            // If revision_requested, keep the status as draft for re-submission
            if ($dar->status === 'revision_requested') {
                $dar->update(['status' => 'draft', 'is_locked' => false]);
            }

            DB::commit();
            return redirect()->route('staff.dar.show', $dar)
                ->with('success', 'Daily Activity Report updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update DAR. Please try again.')->withInput();
        }
    }

    public function submit(DailyActivityReport $dar)
    {
        $user = Auth::guard('web')->user();

        if ($dar->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        if (!$dar->isEditable()) {
            return redirect()->route('staff.dar.show', $dar)
                ->with('error', 'This DAR has already been submitted.');
        }

        if ($dar->entries()->count() === 0) {
            return redirect()->route('staff.dar.show', $dar)
                ->with('error', 'Cannot submit an empty DAR. Please add at least one activity entry.');
        }

        $dar->submit();

        // Notify reporting manager
        if ($dar->reporting_manager_id) {
            Notification::createForAdmin(
                $dar->reporting_manager_id,
                'dar_submitted',
                'DAR Submitted',
                $user->name . ' submitted their Daily Activity Report for ' . $dar->report_date->format('M d, Y'),
                ['dar_id' => $dar->id]
            );

            try {
                $manager = $dar->reportingManager;
                if ($manager && $manager->email) {
                    $dar->load(['user', 'department', 'entries']);
                    Mail::to($manager->email)->send(new DarSubmittedMail($dar));
                }
            } catch (\Exception $e) {
                // Email failure shouldn't block submission
            }
        }

        return redirect()->route('staff.dar.show', $dar)
            ->with('success', 'Daily Activity Report submitted successfully.');
    }

    public function addAddendum(Request $request, DailyActivityReport $dar)
    {
        $user = Auth::guard('web')->user();

        if ($dar->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        if (!$dar->canAddAddendum()) {
            return redirect()->route('staff.dar.show', $dar)
                ->with('error', 'Cannot add addendum to this DAR.');
        }

        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        DarAddendum::create([
            'daily_activity_report_id' => $dar->id,
            'content' => $request->content,
            'added_by' => $user->id,
        ]);

        return redirect()->route('staff.dar.show', $dar)
            ->with('success', 'Addendum added successfully.');
    }
}
