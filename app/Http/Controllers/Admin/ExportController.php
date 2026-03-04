<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\DailyActivityReport;
use App\Exports\TaskReportExport;
use App\Exports\DarMonthlyExport;
use App\Services\TaskReportPdfService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    protected TaskReportPdfService $pdfService;

    public function __construct(TaskReportPdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    // --- Task Exports ---

    public function taskSummaryPdf(Request $request)
    {
        $filters = $request->only(['status', 'department_id', 'priority', 'assigned_to']);
        $pdf = $this->pdfService->generateTaskSummary($filters);
        return $pdf->download('task-summary-report-' . now()->format('Y-m-d') . '.pdf');
    }

    public function taskSummaryExcel(Request $request)
    {
        $filters = $request->only(['status', 'department_id', 'priority', 'assigned_to']);
        return Excel::download(
            new TaskReportExport($filters),
            'task-summary-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function taskDetailPdf(Task $task)
    {
        $pdf = $this->pdfService->generateTaskDetail($task);
        return $pdf->download('task-detail-' . $task->id . '-' . now()->format('Y-m-d') . '.pdf');
    }

    // --- DAR Exports ---

    public function darDetailPdf(DailyActivityReport $dar)
    {
        $pdf = $this->pdfService->generateDarDetail($dar);
        return $pdf->download('dar-' . $dar->user->name . '-' . $dar->report_date->format('Y-m-d') . '.pdf');
    }

    public function darMonthlyPdf(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        $departmentId = $request->get('department_id');

        $pdf = $this->pdfService->generateDarMonthly($year, $month, $departmentId);
        return $pdf->download('dar-monthly-' . $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.pdf');
    }

    public function darMonthlyExcel(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        $departmentId = $request->get('department_id');

        return Excel::download(
            new DarMonthlyExport($year, $month, $departmentId),
            'dar-monthly-' . $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.xlsx'
        );
    }
}
