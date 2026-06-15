<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceReportExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $mode = $this->data['mode'];
        $reportData = $this->data['reportData'];
        $exportArray = [];

        foreach ($reportData as $row) {
            if ($mode === 'day') {
                $exportArray[] = [
                    $row['name'],
                    $row['user_id'],
                    $row['first'],
                    $row['last'],
                    $row['expected'],
                    $row['status'],
                    $row['delay'],
                    $row['worked']
                ];
            } else {
                $exportArray[] = [
                    $row['name'],
                    $row['user_id'],
                    $row['totalDays'],
                    $row['presentDays'],
                    $row['absentDays'],
                    $row['lateDays'],
                    $row['totalDelayMins'] . ' mins',
                    $row['totalWorkedHrs'] . ' hrs'
                ];
            }
        }

        return $exportArray;
    }

    public function headings(): array
    {
        if ($this->data['mode'] === 'day') {
            return [
                'Employee Name',
                'ID',
                'First Punch',
                'Last Punch',
                'Expected',
                'Status',
                'Delay (Mins)',
                'Worked Hrs'
            ];
        } else {
            return [
                'Employee Name',
                'ID',
                'Work Days',
                'Present',
                'Absent',
                'Late',
                'Total Delay',
                'Worked Hrs'
            ];
        }
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF4F46E5']]],
        ];
    }
}
