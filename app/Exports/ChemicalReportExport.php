<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\Business\Chemical\ChemicalLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ChemicalReportExport implements FromView, WithStyles, WithColumnWidths
{
    private array $data;
    public array $companyChemicals = [];
    private array $companyChemicalsFormatted = [];
    private array $filters;
    public const REPORT_NAME = 'CHEMICAL REPORT';
    public const DATE_FORMAT = 'm/d/Y';
    public const CELL_FONT = 'Calibri';

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
            'sections' => $this->fetchChemicalLogs()
        ];

        $this->getCompanyLevelUsedChemicalList();
    }


    /**
     * Get chemical used list accumulated at company level
     */
    private function getCompanyLevelUsedChemicalList(): void
    {
        foreach ($this->companyChemicals as $chem) {
            $this->companyChemicalsFormatted = $this->chemicalExists($chem, $this->companyChemicalsFormatted);
        }
    }

    /**
     * Get chemical logs grouped by customer
     * @return Collection
     */
    public function getChemicalLogsGroupedByCustomer(): Collection
    {
        $query = ChemicalLog::query()
        ->with([
            'usedMaintenanceItems' => function ($query) {
                $query->select('id', 'instance_id', 'item', 'quantity', 'unit', 'remover_added');
            },
            'workOrder.completedJobCustomers',
            'workOrderAssignment.completedJobCustomers',
            'technician' => function ($query) {
                $query->select('id', 'first_name', 'last_name');
            }
        ])
        ->join('work_orders', 'chemical_logs.work_order_id', '=', 'work_orders.id')
        ->where('work_orders.business_id', auth()->guard('business')->user()?->business_id);

        // Apply date filters if provided
        if (!empty($this->filters['startDate'])) {
            $query->whereDate('chemical_logs.created_at', '>=', $this->filters['startDate']);
        }
        if (!empty($this->filters['endDate'])) {
            $query->whereDate('chemical_logs.created_at', '<=', $this->filters['endDate']);
        }

        if (!empty($this->filters['selectedChemicals'])) {
            $query->whereIn('chemical_logs.chemical_name', $this->filters['selectedChemicals']);
        }

        // Apply search filter if provided
        if (!empty($this->filters['search'])) {
            $query->where(function ($q) {
                $q->where('chemical_used', 'like', '%' . $this->filters['search'] . '%')
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

        // Fetch results
        $logs = $query->get();
        // ✅ Deduplicate usedMaintenanceItems per instance_id
        $seenInstanceIds = [];
        foreach ($logs as $log) {
            if (in_array($log->instance_id, $seenInstanceIds)) {
                // We've already loaded this instance_id before, remove the relationship
                $log->setRelation('usedMaintenanceItems', collect());
            } else {
                $seenInstanceIds[] = $log->instance_id;
            }
        }

        // Group logs by customer
        return $logs->groupBy(function ($log) {
            $firstCustomer = $log->workOrder?->completedJobCustomers->first();
            return $firstCustomer?->commercial_pool_details ??
            $firstCustomer?->first_name . ' ' . $firstCustomer?->last_name ?? 'Unknown Customer';
        });
    }

    /**
     * Check if checmical already exist in allChemicals array
     */
    public function chemicalExists($chemical, $allChemicals)
    {

        if ($chemical) {
            $found = false;
            foreach ($allChemicals as &$existingChem) {
                if ($existingChem['chemical'] === $chemical['chemical'] && $existingChem['unit'] === $chemical['unit']) {
                    $existingChem['qty'] += $chemical['qty'];
                    $found = true;
                    break;
                }
            }

            if (!$found && $chemical) {
                $allChemicals[] = $chemical;
            }
        }

        return $allChemicals;
    }

    /**
     * Fetch chemical logs
     * @return array
     */
    public function fetchChemicalLogs(): array
    {
        $logs = $this->getChemicalLogsGroupedByCustomer();

        $sections = [];
        if ($logs->isEmpty()) {
            return $sections;
        }

        foreach ($logs as $customerName => $customerLogs) {
            $firstCustomer = $customerLogs->first()->workOrder?->completedJobCustomers->first();

            // Group chemicals by work order
            $workOrderChemicals = [];
            $allChemicals = [];
            $allTabs = [];

            foreach ($customerLogs as $log) {
                $instanceId = $log?->instance_id;

                if (!isset($workOrderChemicals[$instanceId])) {

                    $completedAt = $log->workOrderAssignment?->completed_at ?? $log->workOrder?->completed_at;
                    $workOrderChemicals[$instanceId] = [
                        'job_id' => $instanceId,
                        'technician' => $log->technician?->first_name . ' ' . $log->technician?->last_name ?? 'Unknown',
                        'date' => Carbon::parse($completedAt)->format(self::DATE_FORMAT),
                        'chemicals' => []
                    ];
                }

                $chemical = [
                    'qty' => $log->qty_added,
                    'unit' => $log->chemical_used_unit,
                    'chemical' => $log->chemical_used
                ];

                $tabs = [];
                if ($log->tabs) {
                    $tabs['qty'] = $log->tabs;
                    $tabs['unit'] = 'tab';
                    $tabs['chemical'] = 'TABS';
                    $workOrderChemicals[$instanceId]['chemicals'][] = $tabs;
                }

                $workOrderChemicals[$instanceId]['chemicals'][] = $chemical;
                if (!empty($log->usedMaintenanceItems->toArray())) {
                    $workOrderChemicals[$instanceId]['maintenance_items'] = $log->usedMaintenanceItems->toArray();
                }

                // Add to all chemicals list
                $allChemicals = $this->chemicalExists($chemical, $allChemicals);
                $allTabs = $this->chemicalExists($tabs, $allTabs);
            }

            $allChemicals = array_merge($allChemicals, $allTabs);
            $this->companyChemicals = array_merge($this->companyChemicals ?? [], $allChemicals);

            // Sort workOrderChemicals by date in descending order
            $sortedWorkOrderChemicals = collect($workOrderChemicals)
                ->sortByDesc(function ($item) {
                    return Carbon::createFromFormat(self::DATE_FORMAT, $item['date'])->timestamp;
                })
                ->values()
                ->all();

            $sections[] = [
                'customer_name' => $customerName,
                'customer_address_1' => $firstCustomer?->address ?? '',
                'customer_address_2' => $firstCustomer?->street ?? '',
                'zip_code' => $firstCustomer?->zip_code ?? '',
                'city' => $firstCustomer?->city ?? '',
                'all_workorder_chemiclas' => $allChemicals,
                'workorders_wise_chemical_data' => $sortedWorkOrderChemicals,
            ];
        }

        return $sections;
    }

    /**
     * @return View
     */
    public function view(): View
    {
        return view('exports.chemical-report', ['data' => $this->data]);
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Style the main header section (title and date)
        $sheet->mergeCells('A1:D3');
        $sheet->getStyle('A1:D3')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'name' => self::CELL_FONT,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'ACB7C8'],
            ],
        ]);

        $period = $this->data['date'] . " To " . $this->data['dateto'];
        $sheet->setCellValue("A1", self::REPORT_NAME . " \n" . $period);

        // Start processing customer sections after header and blank rows
        $currentRow = 6; // Start from row 6 (after header rows 1-3 and blank rows 4-5)
        $companyChemicalEndRow = 6 + count($this->companyChemicalsFormatted);
        $this->rowStyle($sheet, "A{$currentRow}:D{$companyChemicalEndRow}");

        foreach ($this->companyChemicalsFormatted as $chemical) {
            $sheet->setCellValue("B{$currentRow}", $chemical['qty']);
            $sheet->setCellValue("C{$currentRow}", $chemical['unit']);
            $sheet->setCellValue("D{$currentRow}", $chemical['chemical']);
            $currentRow++;
        }

        $currentRow += 2;

        foreach ($this->data['sections'] as $section) {
            // Set customer name and style the row
            $sheet->mergeCells("A{$currentRow}:D{$currentRow}");
            $sheet->setCellValue("A{$currentRow}", $section['customer_name']);
            $sheet->getStyle("A{$currentRow}:D{$currentRow}")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'name' => self::CELL_FONT,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'ACB7C8'],
                ],
            ]);

            // Move to next row for chemical details
            $currentRow++;

            // Get the starting row for this section
            $chemicalCount = count($section['all_workorder_chemiclas']);

            // Merge address cells and set address
            $addressEndRow = $currentRow + $chemicalCount;
            $sheet->mergeCells("A{$currentRow}:A{$addressEndRow}");

            $customerAddress = $section['customer_address_1'] . "\n";
            if ($section['customer_address_2']) {
                $customerAddress .= $section['customer_address_2'] . "\n";
            }
            $customerAddress .= $section['city'] . ", " . $section['zip_code'];

            $sheet->setCellValue("A{$currentRow}", $customerAddress);
            $sheet->getStyle("A{$currentRow}")->getAlignment()
                ->setWrapText(true)
                ->setVertical(Alignment::VERTICAL_CENTER);

            // Set font size, bold and font family for address cell
            $sheet->getStyle("A{$currentRow}")->getFont()
                ->setSize(11)
                ->setBold(true)
                ->setName(self::CELL_FONT);

            // Remove all borders from the address section
            $sheet->getStyle("A{$currentRow}:A{$addressEndRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_NONE,
                    ],
                ],
            ]);

            // Style and populate chemical details
            foreach ($section['all_workorder_chemiclas'] as $chemical) {
                $sheet->setCellValue("B{$currentRow}", $chemical['qty']);
                $sheet->setCellValue("C{$currentRow}", $chemical['unit']);
                $sheet->setCellValue("D{$currentRow}", $chemical['chemical']);

                // Style the entire row
                $this->rowStyle($sheet, "A{$currentRow}:D{$currentRow}");

                // Set specific alignments for each column
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $currentRow++;
            }

            $this->rowStyle($sheet, "A{$currentRow}:D{$currentRow}");

            // Set alignments for total row
            $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            $currentRow++;
            // Process work orders
            foreach ($section['workorders_wise_chemical_data'] as $workOrder) {
                // Add blank row before each work order
                $currentRow++;
                $techRow = $currentRow;

                // Work order header row
                $sheet->setCellValue("E{$currentRow}", $workOrder['technician']);
                $sheet->setCellValue("B{$currentRow}", $workOrder['date']);
                // Add "Job #" prefix to the work order ID
                $sheet->setCellValue("D{$currentRow}", "Job #" . $workOrder['job_id']);

                // Set alignments for header row
                $sheet->getStyle("E{$currentRow}")->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // Add bottom border to cells B-D
                $sheet->getStyle("B{$currentRow}:D{$currentRow}")->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Set small font and Calibri for the entire row
                $sheet->getStyle("A{$currentRow}:D{$currentRow}")->getFont()
                    ->setSize(10)
                    ->setName(self::CELL_FONT);

                // Make date bold
                $sheet->getStyle("B{$currentRow}")->getFont()->setBold(true);

                // Set text color for column A  (technician)
                $sheet->getStyle("E{$currentRow}")->getFont()->setColor(
                    new Color('757071')
                );

                // Set text color for column D  (Job #)
                $sheet->getStyle("D{$currentRow}")->getFont()->setColor(
                    new Color('757071')
                );

                $currentRow++;

                $chemicalRowCount = $techRow + count($workOrder['chemicals']);
                $sheet->mergeCells("E{$techRow}:E{$chemicalRowCount}");

                // Process chemicals for this work order
                foreach ($workOrder['chemicals'] as $chemical) {
                    $sheet->setCellValue("B{$currentRow}", $chemical['qty']);
                    $sheet->setCellValue("C{$currentRow}", $chemical['unit']);
                    $sheet->setCellValue("D{$currentRow}", $chemical['chemical']);

                    // Set alignments for chemical row
                    $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle("D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                    // Set small font for the chemical row
                    $sheet->getStyle("A{$currentRow}:D{$currentRow}")->getFont()->setSize(11);

                    $currentRow++;
                }

                $currentRow = $currentRow + 2;
                $sheet->mergeCells("B{$currentRow}:D{$currentRow}");
                $sheet->setCellValue("B{$currentRow}", "Maintenance Items");
                $this->rowStyle($sheet, "B{$currentRow}:D{$currentRow}");
                $currentRow = $currentRow + 1;

                $currentRow = $this->styleMaintenanceItems($sheet, $currentRow, $workOrder);
            }

            // Add a blank row after the section
            $currentRow++;
        }
    }

    /**
     * Style Maintenance Items Rows
     */
    private function styleMaintenanceItems($sheet, $currentRow, $workOrder)
    {
        if (isset($workOrder['maintenance_items'])) {
            foreach ($workOrder['maintenance_items'] as $chemical) {
                $sheet->setCellValue("B{$currentRow}", $chemical['quantity']);
                $sheet->setCellValue("C{$currentRow}", $chemical['unit']);
                $sheet->setCellValue("D{$currentRow}", $chemical['item']);

                // Set alignments for chemical row
                $sheet->getStyle("B{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("C{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("D{$currentRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // Set small font for the chemical row
                $sheet->getStyle("A{$currentRow}:D{$currentRow}")->getFont()->setSize(11);

                $currentRow++;
            }
        }

        return $currentRow;
    }

    /**
     * Style Row
     */
    public function rowStyle($sheet, $rowColumn)
    {
        $sheet->getStyle($rowColumn)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D6DBE3'],
            ],
            'font' => [
                'size' => 11,
                'name' => self::CELL_FONT,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_NONE,
                ],
            ],
        ]);
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 30,  // Section column
            'B' => 20,  // Description column
            'C' => 10,  // Value column
            'D' => 30,  // Notes column
            'E' => 20,  // Technician column
        ];
    }
}
