<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Department;
use App\Models\User;
use App\Models\TaskComment;
use App\Models\TaskAttachment;
use App\Models\TaskReview;
use App\Models\Notification;
use App\Services\TaskAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Mail\TaskAssignedMail;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['department', 'assignedUser', 'assignedByAdmin']);

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $tasks = $query->orderBy('deadline', 'asc')
            ->orderBy('priority', 'desc')
            ->paginate(15);

        $departments = Department::active()->orderBy('name')->get();

        $stats = [
            'total' => Task::count(),
            'not_started' => Task::where('status', 'not_started')->count(),
            'in_progress' => Task::where('status', 'in_progress')->count(),
            'completed' => Task::where('status', 'completed')->count(),
            'submitted_for_review' => Task::where('status', 'submitted_for_review')->count(),
            'overdue' => Task::where('deadline', '<', now())
                ->whereNotIn('status', ['completed', 'cancelled', 'closed'])->count(),
        ];

        return view('admin.tasks.index', compact('tasks', 'departments', 'stats'));
    }

    public function create()
    {
        $departments = Department::active()->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('admin.tasks.create', compact('departments', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'expected_deliverables' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:not_started,in_progress,completed,on_hold,submitted_for_review,closed,cancelled',
            'deadline' => 'nullable|date',
            'start_date' => 'nullable|date',
            'attachments.*' => 'nullable|file|max:10240'
        ]);

        $validated['assigned_by'] = Auth::guard('admin')->id();

        $task = Task::create($validated);

        // Audit log
        TaskAuditService::logCreation($task, 'admin', Auth::guard('admin')->id());

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $storedFilename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('task-attachments', $storedFilename, 'public');

                TaskAttachment::create([
                    'task_id' => $task->id,
                    'original_filename' => $file->getClientOriginalName(),
                    'stored_filename' => $storedFilename,
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_by' => Auth::guard('admin')->id()
                ]);
            }
        }

        // Send notification and email to assigned user
        if ($task->assigned_to) {
            Notification::createForUser(
                $task->assigned_to,
                'task_assigned',
                'New Task Assigned',
                "You have been assigned a new task: {$task->title}",
                ['task_id' => $task->id]
            );

            try {
                $assignedUser = User::find($task->assigned_to);
                if ($assignedUser && $assignedUser->email) {
                    $task->load('department');
                    $adminName = Auth::guard('admin')->user()->name;
                    Mail::send(new TaskAssignedMail($task, $assignedUser, $adminName));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send task assignment email: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task created successfully!');
    }

    public function show(Task $task)
    {
        $task->load([
            'department', 'assignedUser', 'assignedByAdmin', 'closedByAdmin',
            'comments.admin', 'comments.user',
            'attachments.uploader',
            'progressLogs.user', 'progressLogs.attachments',
            'reviews.admin',
            'auditLogs',
        ]);

        return view('admin.tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        if (!$task->isEditable()) {
            return back()->with('error', 'This task is locked and cannot be edited.');
        }

        $departments = Department::active()->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('admin.tasks.edit', compact('task', 'departments', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        if (!$task->isEditable()) {
            return back()->with('error', 'This task is locked and cannot be edited.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'expected_deliverables' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:not_started,in_progress,completed,on_hold,submitted_for_review,closed,cancelled',
            'deadline' => 'nullable|date',
            'start_date' => 'nullable|date',
            'progress_percentage' => 'required|integer|min:0|max:100',
            'attachments.*' => 'nullable|file|max:10240'
        ]);

        $adminId = Auth::guard('admin')->id();
        $oldStatus = $task->status;
        $oldAssignedTo = $task->assigned_to;

        // Audit field changes
        $fieldsToAudit = ['title', 'description', 'expected_deliverables', 'priority', 'deadline', 'start_date', 'assigned_to', 'progress_percentage'];
        foreach ($fieldsToAudit as $field) {
            if (isset($validated[$field]) && (string)$task->{$field} !== (string)($validated[$field] ?? '')) {
                TaskAuditService::logFieldChange($task, $field, (string)$task->{$field}, (string)$validated[$field], 'admin', $adminId);
            }
        }

        // Set completed_at if status changed to completed
        if ($validated['status'] === 'completed' && $oldStatus !== 'completed') {
            $validated['completed_at'] = now();
            $validated['progress_percentage'] = 100;
        }

        // Audit status change
        if ($validated['status'] !== $oldStatus) {
            TaskAuditService::logStatusChange($task, $oldStatus, $validated['status'], 'admin', $adminId);
        }

        $task->update($validated);

        // Handle new attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $storedFilename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('task-attachments', $storedFilename, 'public');

                TaskAttachment::create([
                    'task_id' => $task->id,
                    'original_filename' => $file->getClientOriginalName(),
                    'stored_filename' => $storedFilename,
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_by' => $adminId
                ]);
            }
        }

        // Notify if assigned user changed
        if ($task->assigned_to && $task->assigned_to !== $oldAssignedTo) {
            Notification::createForUser(
                $task->assigned_to,
                'task_assigned',
                'New Task Assigned',
                "You have been assigned a task: {$task->title}",
                ['task_id' => $task->id]
            );

            try {
                $assignedUser = User::find($task->assigned_to);
                if ($assignedUser && $assignedUser->email) {
                    $task->load('department');
                    $adminName = Auth::guard('admin')->user()->name;
                    Mail::send(new TaskAssignedMail($task, $assignedUser, $adminName));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send task assignment email: ' . $e->getMessage());
            }
        }

        // Notify if status changed
        if ($task->assigned_to && $validated['status'] !== $oldStatus) {
            Notification::createForUser(
                $task->assigned_to,
                'task_updated',
                'Task Status Updated',
                "Task '{$task->title}' status changed to " . ucfirst(str_replace('_', ' ', $validated['status'])),
                ['task_id' => $task->id]
            );
        }

        return redirect()->route('admin.tasks.show', $task)
            ->with('success', 'Task updated successfully!');
    }

    public function destroy(Task $task)
    {
        foreach ($task->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $task->delete();

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task deleted successfully!');
    }

    public function addComment(Request $request, Task $task)
    {
        $request->validate([
            'comment' => 'required|string'
        ]);

        $adminId = Auth::guard('admin')->id();

        $comment = TaskComment::create([
            'task_id' => $task->id,
            'admin_id' => $adminId,
            'comment' => $request->comment
        ]);

        TaskAuditService::logComment($task, $comment, 'admin', $adminId);

        if ($task->assigned_to) {
            Notification::createForUser(
                $task->assigned_to,
                'comment_added',
                'New Comment on Task',
                "New comment added to task: {$task->title}",
                ['task_id' => $task->id, 'comment_id' => $comment->id]
            );
        }

        return back()->with('success', 'Comment added successfully!');
    }

    public function deleteComment(Task $task, TaskComment $comment)
    {
        if ($comment->task_id !== $task->id) {
            return back()->with('error', 'Comment does not belong to this task.');
        }

        $comment->delete();

        return back()->with('success', 'Comment deleted successfully!');
    }

    public function deleteAttachment(Task $task, TaskAttachment $attachment)
    {
        if ($attachment->task_id !== $task->id) {
            return back()->with('error', 'Attachment does not belong to this task.');
        }

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return back()->with('success', 'Attachment deleted successfully!');
    }

    public function generateShareLink(Task $task)
    {
        $task->generateShareToken();
        return back()->with('success', 'Share link generated successfully!');
    }

    public function revokeShareLink(Task $task)
    {
        $task->revokeShareToken();
        return back()->with('success', 'Share link revoked successfully!');
    }

    public function updateProgress(Request $request, Task $task)
    {
        $request->validate([
            'progress_percentage' => 'required|integer|min:0|max:100'
        ]);

        $oldProgress = $task->progress_percentage;
        $task->update([
            'progress_percentage' => $request->progress_percentage
        ]);

        TaskAuditService::logFieldChange($task, 'progress_percentage', (string)$oldProgress, (string)$request->progress_percentage, 'admin', Auth::guard('admin')->id());

        if ($request->progress_percentage == 100 && $task->status !== 'completed') {
            $task->markAsCompleted();
        }

        return response()->json(['success' => true, 'message' => 'Progress updated successfully!']);
    }

    // Manager Review
    public function submitReview(Request $request, Task $task)
    {
        $request->validate([
            'action' => 'required|in:remark,revision_requested,approved,closed',
            'remarks' => 'required|string',
        ]);

        $adminId = Auth::guard('admin')->id();

        $review = TaskReview::create([
            'task_id' => $task->id,
            'admin_id' => $adminId,
            'action' => $request->action,
            'remarks' => $request->remarks,
        ]);

        // Handle action
        switch ($request->action) {
            case 'approved':
                $oldStatus = $task->status;
                $task->approve();
                TaskAuditService::logStatusChange($task, $oldStatus, 'completed', 'admin', $adminId);
                TaskAuditService::logReview($task, 'approved', 'admin', $adminId, $request->remarks);
                break;

            case 'revision_requested':
                $oldStatus = $task->status;
                $task->requestRevision();
                TaskAuditService::logStatusChange($task, $oldStatus, 'in_progress', 'admin', $adminId);
                TaskAuditService::logReopened($task, 'admin', $adminId);
                break;

            case 'closed':
                $oldStatus = $task->status;
                $task->close($adminId);
                TaskAuditService::logStatusChange($task, $oldStatus, 'closed', 'admin', $adminId);
                TaskAuditService::logClosed($task, 'admin', $adminId);
                break;

            case 'remark':
                TaskAuditService::logReview($task, 'remark', 'admin', $adminId, $request->remarks);
                break;
        }

        // Notify assigned user
        if ($task->assigned_to) {
            $actionLabels = [
                'approved' => 'Task Approved',
                'revision_requested' => 'Revision Requested',
                'closed' => 'Task Closed',
                'remark' => 'Manager Remark',
            ];

            Notification::createForUser(
                $task->assigned_to,
                'task_updated',
                $actionLabels[$request->action] ?? 'Task Updated',
                "Task '{$task->title}': " . ($actionLabels[$request->action] ?? 'Updated') . " - {$request->remarks}",
                ['task_id' => $task->id, 'review_id' => $review->id]
            );
        }

        return back()->with('success', 'Review submitted successfully!');
    }

    // Public view for external sharing
    public function publicView($token)
    {
        $task = Task::where('external_share_token', $token)
            ->where('is_external_shared', true)
            ->with(['department', 'comments', 'attachments'])
            ->firstOrFail();

        return view('admin.tasks.public-view', compact('task'));
    }
}
