<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Department;
use App\Models\User;
use App\Models\TaskComment;
use App\Models\TaskAttachment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['department', 'assignedUser', 'assignedByAdmin']);

        // Filters
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

        // Dashboard stats
        $stats = [
            'total' => Task::count(),
            'pending' => Task::where('status', 'pending')->count(),
            'in_progress' => Task::where('status', 'in_progress')->count(),
            'completed' => Task::where('status', 'completed')->count(),
            'overdue' => Task::where('deadline', '<', now())
                ->whereNotIn('status', ['completed', 'cancelled'])->count(),
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
            'department_id' => 'nullable|exists:departments,id',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,completed,on_hold,cancelled',
            'deadline' => 'nullable|date',
            'attachments.*' => 'nullable|file|max:10240' // 10MB max per file
        ]);

        $validated['assigned_by'] = Auth::guard('admin')->id();

        $task = Task::create($validated);

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

        // Send notification to assigned user
        if ($task->assigned_to) {
            Notification::createForUser(
                $task->assigned_to,
                'task_assigned',
                'New Task Assigned',
                "You have been assigned a new task: {$task->title}",
                ['task_id' => $task->id]
            );
        }

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task created successfully!');
    }

    public function show(Task $task)
    {
        $task->load(['department', 'assignedUser', 'assignedByAdmin', 'comments.admin', 'comments.user', 'attachments.uploader']);

        return view('admin.tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $departments = Department::active()->orderBy('name')->get();
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('admin.tasks.edit', compact('task', 'departments', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,completed,on_hold,cancelled',
            'deadline' => 'nullable|date',
            'progress_percentage' => 'required|integer|min:0|max:100',
            'attachments.*' => 'nullable|file|max:10240'
        ]);

        $oldStatus = $task->status;
        $oldAssignedTo = $task->assigned_to;

        // Set completed_at if status changed to completed
        if ($validated['status'] === 'completed' && $oldStatus !== 'completed') {
            $validated['completed_at'] = now();
            $validated['progress_percentage'] = 100;
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
                    'uploaded_by' => Auth::guard('admin')->id()
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
        // Delete attachments from storage
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

        $comment = TaskComment::create([
            'task_id' => $task->id,
            'admin_id' => Auth::guard('admin')->id(),
            'comment' => $request->comment
        ]);

        // Notify assigned user about new comment
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
        $token = $task->generateShareToken();

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

        $task->update([
            'progress_percentage' => $request->progress_percentage
        ]);

        // Auto-complete if 100%
        if ($request->progress_percentage == 100 && $task->status !== 'completed') {
            $task->markAsCompleted();
        }

        return response()->json(['success' => true, 'message' => 'Progress updated successfully!']);
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
