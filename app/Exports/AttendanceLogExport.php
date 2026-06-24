<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceLogExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return __('Attendance Records');
    }

    public function collection()
    {
        $rows = collect();
        $index = 0;

        foreach ($this->data as $log) {
            $index++;
            $calculatedStatus = $log->calculated_status ?? __('Unknown');
            
            $rows->push((object) [
                'index' => $index,
                'name' => $log->user ? $log->user->name : $log->user_name,
                'user_id' => $log->user_id,
                'timestamp' => $log->timestamp,
                'state' => $log->state == 1 ? __('Check-In') : ($log->state == 2 ? __('Check-Out') : ($log->state == 4 ? __('Overtime In') : ($log->state == 5 ? __('Overtime Out') : __('Unknown')))),
                'calculated_status' => __($calculatedStatus),
                'device_id' => $log->device_id,
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            '#',
            __('Employee Name'),
            __('Employee ID'),
            __('Timestamp'),
            __('Device State'),
            __('Calculated Status'),
            __('Device ID')
        ];
    }

    public function map($row): array
    {
        return [
            $row->index,
            $row->name,
            $row->user_id,
            $row->timestamp,
            $row->state,
            $row->calculated_status,
            $row->device_id,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();
        
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '1E293B',
                ],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FFCCCCCC'],
                ],
            ],
        ]);
        
        $sheet->getStyle('A2:' . $lastCol . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FFDDDDDD'],
                ],
            ],
        ]);
        
        return [];
    }
}
