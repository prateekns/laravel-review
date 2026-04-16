<?php

namespace App\Exports;

use App\Models\Business\Business;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * @author AI
 * @description Exports businesses with requested columns in XLS format.
 */
class BusinessExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * Get the data collection for export.
     *
     * @return Collection
     */
    public function collection(): Collection
    {
        return Business::with([
            'primaryUser', 'country', 'state', 'city'
        ])->get();
    }

    /**
     * Map data for each row.
     *
     * @param Business $business
     * @return array
     */
    public function map($business): array
    {
        $admin = $business->primaryUser;
        $phone = $business->phone ? $business->isd_code.$business->phone : '';
        return [
            $business->name,
            $admin ? ($admin->first_name . ' ' . $admin->last_name) : '',
            $admin?->email ?? '',
            $phone,
            $business->website_url,
            $business->address,
            $business->country?->name ?? '',
            $business->state?->name ?? '',
            $business->city?->name ?? '',
            $business->street,
            $business->zipcode, // Use the column name as in migration
        ];
    }

    /**
     * Define column headings.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Business Name',
            'Business Admin Name',
            'Business Email',
            'Business Phone',
            'Website',
            'Address',
            'Country',
            'State',
            'City',
            'Street',
            'Zip Code',
        ];
    }

    /**
     * Style the header row.
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
