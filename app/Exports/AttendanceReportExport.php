<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class AttendanceReportExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Attendance Report';
    }

    public function collection()
    {
        $mode = $this->data['mode'];
        $reportData = $this->data['reportData'];
        $rows = collect();
        $index = 0;

        foreach ($reportData as $row) {
            $index++;
            if ($mode === 'day') {
                $rows->push((object) [
                    'index' => $index,
                    'name' => $row['name'],
                    'user_id' => $row['user_id'],
                    'first' => $row['first'],
                    'last' => $row['last'],
                    'expected' => $row['expected'],
                    'status' => $row['status'],
                    'delay' => $row['delay'],
                    'worked' => $row['worked'],
                ]);
            } else {
                $rows->push((object) [
                    'index' => $index,
                    'name' => $row['name'],
                    'user_id' => $row['user_id'],
                    'totalDays' => $row['totalDays'],
                    'presentDays' => $row['presentDays'],
                    'absentDays' => $row['absentDays'],
                    'lateDays' => $row['lateDays'],
                    'totalDelayMins' => $row['totalDelayMins'] . ' mins',
                    'totalWorkedHrs' => $row['totalWorkedHrs'] . ' hrs',
                ]);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        if ($this->data['mode'] === 'day') {
            return ['#', 'Employee Name', 'ID', 'First Punch', 'Last Punch', 'Expected', 'Status', 'Delay', 'Worked Hrs'];
        } else {
            return ['#', 'Employee Name', 'ID', 'Work Days', 'Present', 'Absent', 'Late', 'Total Delay', 'Worked Hrs'];
        }
    }

    public function map($row): array
    {
        $mode = $this->data['mode'];

        if ($mode === 'day') {
            return [
                $row->index,
                $row->name,
                $row->user_id,
                $row->first,
                $row->last,
                $row->expected,
                $row->status,
                $row->delay,
                $row->worked,
            ];
        } else {
            return [
                $row->index,
                $row->name,
                $row->user_id,
                $row->totalDays,
                $row->presentDays,
                $row->absentDays,
                $row->lateDays,
                $row->totalDelayMins,
                $row->totalWorkedHrs,
            ];
        }
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();
        $mode = $this->data['mode'];
        $styles = [];

        $styles[1] = [
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 11,
                'name' => 'Arial',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF3B82F6'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        for ($row = 2; $row <= $lastRow; $row++) {
            $isEven = $row % 2 === 0;

            $styles[$row] = [
                'font' => [
                    'size' => 10,
                    'name' => 'Arial',
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => $isEven ? 'FFF1F5F9' : 'FFFFFFFF'],
                ],
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']],
                    'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']],
                    'left' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']],
                    'right' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ];

            if ($mode === 'day') {
                $statusCol = 'G';
                $statusCell = $sheet->getCell($statusCol . $row)->getValue();

                if ($statusCell === 'On Time') {
                    $styles[$row]['font']['color'] = ['argb' => 'FF16A34A'];
                    $styles[$row]['font']['bold'] = true;
                } elseif ($statusCell === 'Late') {
                    $styles[$row]['font']['color'] = ['argb' => 'FFDC2626'];
                    $styles[$row]['font']['bold'] = true;
                } elseif ($statusCell === 'Absent') {
                    $styles[$row]['font']['color'] = ['argb' => 'FF9CA3AF'];
                    $styles[$row]['font']['italic'] = true;
                } elseif ($statusCell === 'Off Day') {
                    $styles[$row]['font']['color'] = ['argb' => 'FF6366F1'];
                }
            }
        }

        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(14);
        $sheet->getColumnDimension('E')->setWidth(14);
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->getColumnDimension('G')->setWidth(14);
        $sheet->getColumnDimension('H')->setWidth(14);
        $sheet->getColumnDimension('I')->setWidth(14);

        $sheet->freezePane('A2');

        return $styles;
    }
}
