<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Business\WorkOrder\ItemSold;
use Carbon\Carbon;

class ItemSoldReportExport implements FromView, WithStyles, WithColumnWidths
{
    private array $data;
    private array $filters;
    public const HEADERS = ['Date', 'Technician', 'Customer', 'Item Sold', 'Quantity', 'Additional Items Sold/Extra Work','Job Name'];
    public const REPORT_NAME = 'ITEMS SOLD REPORT';
    public const DATE_FORMAT = 'm/d/Y';
    public const CELL_FONT = 'Calibri';
    public const COLUMN_WIDTHS = [
        'A' => 15,  // Date column
        'B' => 20,  // Customer column
        'C' => 20,  // Item Sold
        'D' => 10,  // Quantity column
        'E' => 10,  // Additional Items column
        'F' => 40,  // Job Name column
        'G' => 30,  // Job ID column
    ];

    /**
     * @param array $filters
     */
    public function __construct(array $filters = [])
    {
        $this->filters = $filters;

        $this->data = [
            'title' => self::REPORT_NAME,
            'date' => isset($filters['startDate']) ? Carbon::parse($filters['startDate'])->format(self::DATE_FORMAT) : now()->format(self::DATE_FORMAT),
            'dateto' => isset($filters['endDate']) ? Carbon::parse($filters['endDate'])->format(self::DATE_FORMAT) : now()->format(self::DATE_FORMAT),
            'records' => $this->fetchItemsSold()
        ];
    }

    /**
     * Fetch items sold
     * @return array
     */
    public function fetchItemsSold(): array
    {
        $query = ItemSold::query()
            ->with([
                'workOrder.completedJobCustomers' => function ($query) {
                    $query->select('id', 'customer_id', 'work_order_id', 'instance_id', 'commercial_pool_details', 'first_name', 'last_name');
                },
                'workOrderAssignment.completedJobCustomers' => function ($query) {
                    $query->select('id', 'customer_id', 'work_order_id', 'instance_id', 'commercial_pool_details', 'first_name', 'last_name');
                },
            ])
            ->where('business_id', auth()->guard('business')->user()?->business_id);

        // Apply date filters if provided
        if (!empty($this->filters['startDate'])) {
            $query->whereDate('created_at', '>=', $this->filters['startDate']);
        }
        if (!empty($this->filters['endDate'])) {
            $query->whereDate('created_at', '<=', $this->filters['endDate']);
        }

        // Apply search filter if provided
        if (!empty($this->filters['search'])) {
            $query->where(function ($q) {
                $q->where('item', 'like', '%' . $this->filters['search'] . '%')
                    ->orWhereHas('workOrder.completedJobCustomers', function ($q) {
                        $q->where('first_name', 'like', '%' . $this->filters['search'] . '%');
                    })
                    ->orWhereHas('workOrder.completedJobCustomers', function ($q) {
                        $q->where('last_name', 'like', '%' . $this->filters['search'] . '%');
                    })
                    ->orWhereHas('workOrderAssignment.completedJobCustomers', function ($q) {
                        $q->where('company_name', 'like', '%' . $this->filters['search'] . '%');
                    });
            });
        }

        return $query->get()->map(function ($record) {
            $customer = null;
            $extraWorkDone = null;
            $completedAt = null;

            if ($record->workOrderAssignment) {
                $customer = $record->workOrderAssignment->completedJobCustomers->first();
                $extraWorkDone = $record->workOrderAssignment->extra_work_done;
                $completedAt = $record->workOrderAssignment->completed_at;
            } elseif ($record->workOrder) {
                $customer = $record->workOrder->completedJobCustomers->first();
                $extraWorkDone = $record->workOrder->extra_work_done;
                $completedAt = $record->workOrder->completed_at;
            }

            return [
                'work_order_id' => $record->work_order_id ?? '-',
                'instance_id' => $record->instance_id ?? '-',
                'job_name' => $record->workOrder?->name ?? '-',
                'item' => $record->item ?? '-',
                'customer_name' => $customer?->commercial_pool_details ?? $customer?->first_name . ' ' . $customer?->last_name ?? '-',
                'technician' => $record->workOrder?->technician?->first_name . ' ' . $record->workOrder?->technician?->last_name ?? '-',
                'quantity' => $record->quantity ?? 0,
                'additional_items' => $extraWorkDone ?? '-',
                'date' => $completedAt ? Carbon::parse($completedAt)
                    ->setTimezone(auth()->guard('business')->user()?->business?->timezone ?? config('datetime.timezones.default'))
                    ->format('m/d/Y') : '-'
            ];
        })->toArray();
    }

    /**
     * @return View
     */
    public function view(): View
    {
        return view('exports.items-sold-report', ['data' => $this->data]);
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Style the main header section (title and date)
        $sheet->mergeCells('A1:G3');
        $sheet->getStyle('A1:G3')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'name' => self::CELL_FONT,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'ACB7C8'],
            ],
        ]);

        $period = $this->data['date'] . " To " . $this->data['dateto'];
        $sheet->setCellValue("A1", self::REPORT_NAME . " \n" . $period);

        // Set headers at row 5
        $currentRow = 5;

        foreach (self::HEADERS as $col => $header) {
            $column = chr(65 + $col); // Convert 0,1,2,3,4 to A,B,C,D,E
            $sheet->setCellValue("{$column}{$currentRow}", $header);
            $sheet->getStyle("{$column}{$currentRow}")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 11,
                    'name' => 'Calibri',
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D6DBE3'],
                ],
            ]);
        }

        $currentRow++;

        // Add data rows
        foreach ($this->data['records'] as $record) {
            $sheet->setCellValue("A{$currentRow}", $record['date']);
            $sheet->setCellValue("B{$currentRow}", $record['technician']);
            $sheet->setCellValue("C{$currentRow}", $record['customer_name']);
            $sheet->setCellValue("D{$currentRow}", $record['item']);
            $sheet->setCellValue("E{$currentRow}", $record['quantity']);
            $sheet->setCellValue("F{$currentRow}", $record['additional_items']);
            $sheet->setCellValue("G{$currentRow}", $record['job_name']);

            // Style the row
            $sheet->getStyle("A{$currentRow}:E{$currentRow}")->applyFromArray([
                'font' => [
                    'size' => 11,
                    'name' => self::CELL_FONT,
                ],
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Set specific alignments
            $sheet->getStyle("C{$currentRow}")->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $currentRow++;
        }

        $sheet->getStyle('F')->getAlignment()->setWrapText(true);
        $sheet->getStyle('G')->getAlignment()->setWrapText(true);
    }

    /**
     * @return array Column widths
     */
    public function columnWidths(): array
    {
        return self::COLUMN_WIDTHS;
    }
}
