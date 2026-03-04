<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskProgressLog;
use App\Models\ProgressLogAttachment;
use App\Models\Notification;
use App\Services\TaskAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('web')->user();

        $query = Task::where('assigned_to', $user->id)
            ->with(['department', 'assignedByAdmin']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $tasks = $query->orderBy('deadline', 'asc')
            ->orderBy('priority', 'desc')
            ->paginate(15);

        $stats = [
            'total' => Task::where('assigned_to', $user->id)->count(),
            'in_progress' => Task::where('assigned_to', $user->id)->where('status', 'in_progress')->count(),
            'submitted_for_review' => Task::where('assigned_to', $user->id)->where('status', 'submitted_for_review')->count(),
            'overdue' => Task::where('assigned_to', $user->id)
                ->where('deadline', '<', now())
                ->whereNotIn('status', ['completed', 'cancelled', 'closed'])->count(),
        ];

        return view('staff.tasks.index', compact('tasks', 'stats'));
    }

    public function show(Task $task)
    {
        $user = Auth::guard('web')->user();

        // Verify ownership
        if ($task->assigned_to !== $user->id) {
            abort(403, 'You are not authorized to view this task.');
        }

        $task->load([
            'department', 'assignedByAdmin', 'closedByAdmin',
            'comments.admin', 'comments.user',
            'attachments',
            'progressLogs.user', 'progressLogs.attachments',
            'reviews.admin',
        ]);

        // Check if today's progress log exists
        $todayLog = $task->progressLogs()
            ->where('user_id', $user->id)
            ->where('log_date', now()->toDateString())
            ->first();

        return view('staff.tasks.show', compact('task', 'todayLog'));
    }

    public function submitProgress(Request $request, Task $task)
    {
        $user = Auth::guard('web')->user();

        if ($task->assigned_to !== $user->id) {
            abort(403);
        }

        if (!$task->isEditableByEmployee()) {
            return back()->with('error', 'This task cannot be updated at this time.');
        }

        $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100',
            'description' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        $today = now()->toDateString();

        // Check if a log for today already exists
        $log = TaskProgressLog::where('task_id', $task->id)
            ->where('user_id', $user->id)
            ->where('log_date', $today)
            ->first();

        if ($log && $log->is_locked) {
            return back()->with('error', 'Today\'s progress has already been submitted and locked.');
        }

        if ($log) {
            // Update existing draft
            $log->update([
                'progress_percentage' => $request->progress_percentage,
                'description' => $request->description,
            ]);
        } else {
            // Create new log
            $log = TaskProgressLog::create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'log_date' => $today,
                'progress_percentage' => $request->progress_percentage,
                'description' => $request->description,
            ]);
        }

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $storedFilename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('progress-attachments', $storedFilename, 'public');

                ProgressLogAttachment::create([
                    'progress_log_id' => $log->id,
                    'original_filename' => $file->getClientOriginalName(),
                    'stored_filename' => $storedFilename,
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }

        // Lock the log (submit it)
        $log->lock();

        // Audit log
        TaskAuditService::logProgressUpdate($task, 'user', $user->id, [
            'progress_percentage' => $log->progress_percentage,
            'progress_log_id' => $log->id,
        ]);

        // Notify the admin who assigned the task
        if ($task->assigned_by) {
            Notification::createForAdmin(
                $task->assigned_by,
                'progress_update',
                'Progress Update',
                "{$user->name} submitted daily progress ({$request->progress_percentage}%) for task: {$task->title}",
                ['task_id' => $task->id, 'progress_log_id' => $log->id]
            );
        }

        return back()->with('success', 'Daily progress submitted successfully!');
    }

    public function submitForReview(Request $request, Task $task)
    {
        $user = Auth::guard('web')->user();

        if ($task->assigned_to !== $user->id) {
            abort(403);
        }

        if (!$task->canSubmitForReview()) {
            return back()->with('error', 'This task cannot be submitted for review at this time.');
        }

        $oldStatus = $task->status;
        $task->submitForReview();

        TaskAuditService::logStatusChange($task, $oldStatus, 'submitted_for_review', 'user', $user->id);

        // Notify the admin who assigned the task
        if ($task->assigned_by) {
            Notification::createForAdmin(
                $task->assigned_by,
                'task_submitted_for_review',
                'Task Submitted for Review',
                "{$user->name} submitted task '{$task->title}' for review.",
                ['task_id' => $task->id]
            );
        }

        return back()->with('success', 'Task submitted for review successfully!');
    }

    public function addComment(Request $request, Task $task)
    {
        $user = Auth::guard('web')->user();

        if ($task->assigned_to !== $user->id) {
            abort(403);
        }

        if ($task->is_locked) {
            return back()->with('error', 'This task is locked. No comments can be added.');
        }

        $request->validate([
            'comment' => 'required|string',
        ]);

        $comment = TaskComment::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'comment' => $request->comment,
        ]);

        TaskAuditService::logComment($task, $comment, 'user', $user->id);

        // Notify the admin
        if ($task->assigned_by) {
            Notification::createForAdmin(
                $task->assigned_by,
                'comment_added',
                'New Comment on Task',
                "{$user->name} commented on task: {$task->title}",
                ['task_id' => $task->id, 'comment_id' => $comment->id]
            );
        }

        return back()->with('success', 'Comment added successfully!');
    }
}
