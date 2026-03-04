<?php

namespace App\Exports;

use App\Models\DailyActivityReport;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DarMonthlyExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected int $year;
    protected int $month;
    protected ?int $departmentId;

    public function __construct(int $year, int $month, ?int $departmentId = null)
    {
        $this->year = $year;
        $this->month = $month;
        $this->departmentId = $departmentId;
    }

    public function headings(): array
    {
        $daysInMonth = Carbon::create($this->year, $this->month)->daysInMonth;
        $headers = ['Employee', 'Department'];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($this->year, $this->month, $day);
            $headers[] = $day . ' (' . substr($date->format('D'), 0, 2) . ')';
        }

        $headers[] = 'Submitted';
        $headers[] = 'Working Days';
        $headers[] = 'Compliance %';

        return $headers;
    }

    public function array(): array
    {
        $daysInMonth = Carbon::create($this->year, $this->month)->daysInMonth;

        $query = User::where('role', 'staff')->where('is_active', true);
        if ($this->departmentId) {
            $query->where('department_id', $this->departmentId);
        }
        $staffUsers = $query->with('department')->orderBy('name')->get();

        $dars = DailyActivityReport::forMonth($this->year, $this->month)
            ->when($this->departmentId, fn($q) => $q->forDepartment($this->departmentId))
            ->get()
            ->groupBy('user_id');

        $rows = [];
        foreach ($staffUsers as $staff) {
            $userDars = $dars->get($staff->id, collect());
            $row = [
                $staff->name,
                $staff->department->name ?? 'N/A',
            ];

            $submittedCount = 0;
            $workingDays = 0;

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = Carbon::create($this->year, $this->month, $day);
                $isWeekend = $date->isWeekend();

                if (!$isWeekend) {
                    $workingDays++;
                }

                $dar = $userDars->firstWhere('report_date', $date->toDateString());

                if ($dar) {
                    $status = match ($dar->status) {
                        'reviewed' => 'R',
                        'submitted' => 'S',
                        'revision_requested' => 'V',
                        'draft' => 'D',
                        default => '-',
                    };
                    if (in_array($dar->status, ['submitted', 'reviewed'])) {
                        $submittedCount++;
                    }
                    $row[] = $status;
                } elseif ($isWeekend) {
                    $row[] = 'W';
                } elseif ($date->isPast()) {
                    $row[] = 'X';
                } else {
                    $row[] = '-';
                }
            }

            $row[] = $submittedCount;
            $row[] = $workingDays;
            $row[] = $workingDays > 0 ? round(($submittedCount / $workingDays) * 100) . '%' : '0%';

            $rows[] = $row;
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11]],
        ];
    }
}
