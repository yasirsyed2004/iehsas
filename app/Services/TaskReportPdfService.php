<?php

namespace App\Services;

use App\Models\Task;
use App\Models\DailyActivityReport;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class TaskReportPdfService
{
    public function generateTaskSummary(array $filters = [])
    {
        $query = Task::with(['department', 'assignedUser', 'assignedByAdmin']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }
        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }
        if (!empty($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        $tasks = $query->orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView('pdf.task-summary', [
            'tasks' => $tasks,
            'filters' => $filters,
            'generatedAt' => now(),
        ]);

        $pdf->setPaper('a4', 'landscape');
        return $pdf;
    }

    public function generateTaskDetail(Task $task)
    {
        $task->load([
            'department',
            'assignedUser',
            'assignedByAdmin',
            'comments.user',
            'comments.admin',
            'progressLogs.user',
            'reviews.admin',
            'auditLogs',
        ]);

        $pdf = Pdf::loadView('pdf.task-detail', [
            'task' => $task,
            'generatedAt' => now(),
        ]);

        $pdf->setPaper('a4', 'portrait');
        return $pdf;
    }

    public function generateDarDetail(DailyActivityReport $dar)
    {
        $dar->load(['user', 'department', 'entries.relatedTask', 'addendums.addedBy', 'reviewer']);

        $pdf = Pdf::loadView('pdf.dar-detail', [
            'dar' => $dar,
            'generatedAt' => now(),
        ]);

        $pdf->setPaper('a4', 'portrait');
        return $pdf;
    }

    public function generateDarMonthly(int $year, int $month, ?int $departmentId = null)
    {
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;

        $query = User::where('role', 'staff')->where('is_active', true);
        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }
        $staffUsers = $query->with('department')->orderBy('name')->get();

        $dars = DailyActivityReport::forMonth($year, $month)
            ->when($departmentId, fn($q) => $q->forDepartment($departmentId))
            ->get()
            ->groupBy('user_id');

        $calendarData = [];
        foreach ($staffUsers as $staff) {
            $userDars = $dars->get($staff->id, collect());
            $days = [];
            $submittedCount = 0;
            $workingDays = 0;

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = Carbon::create($year, $month, $day);
                $isWeekend = $date->isWeekend();
                if (!$isWeekend) $workingDays++;

                $dar = $userDars->firstWhere('report_date', $date->toDateString());
                if ($dar && in_array($dar->status, ['submitted', 'reviewed'])) {
                    $submittedCount++;
                }

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
                'submitted_count' => $submittedCount,
                'total_working_days' => $workingDays,
            ];
        }

        $pdf = Pdf::loadView('pdf.dar-monthly', [
            'calendarData' => $calendarData,
            'year' => $year,
            'month' => $month,
            'daysInMonth' => $daysInMonth,
            'generatedAt' => now(),
        ]);

        $pdf->setPaper('a4', 'landscape');
        return $pdf;
    }
}
