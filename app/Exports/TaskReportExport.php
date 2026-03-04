<?php

namespace App\Exports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TaskReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Task::with(['department', 'assignedUser', 'assignedByAdmin']);

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['department_id'])) {
            $query->where('department_id', $this->filters['department_id']);
        }

        if (!empty($this->filters['priority'])) {
            $query->where('priority', $this->filters['priority']);
        }

        if (!empty($this->filters['assigned_to'])) {
            $query->where('assigned_to', $this->filters['assigned_to']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Department',
            'Assigned To',
            'Assigned By',
            'Priority',
            'Status',
            'Progress %',
            'Start Date',
            'Deadline',
            'Created At',
            'Completed At',
        ];
    }

    public function map($task): array
    {
        return [
            $task->id,
            $task->title,
            $task->department->name ?? 'N/A',
            $task->assignedUser->name ?? 'N/A',
            $task->assignedByAdmin->name ?? 'N/A',
            ucfirst($task->priority),
            Task::getStatuses()[$task->status] ?? ucfirst(str_replace('_', ' ', $task->status)),
            $task->progress_percentage . '%',
            $task->start_date?->format('Y-m-d') ?? '',
            $task->deadline?->format('Y-m-d') ?? '',
            $task->created_at->format('Y-m-d H:i'),
            $task->completed_at?->format('Y-m-d H:i') ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
