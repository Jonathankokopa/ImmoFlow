<?php

namespace App\Exports\Sheets;

use App\Models\MaintenanceExpense;
use App\Support\Currency;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MaintenanceSheetExport implements
    FromCollection,
    WithTitle,
    WithHeadings,
    WithStyles,
    ShouldAutoSize
{
    protected Carbon $from;
    protected Carbon $to;
    protected string $selectedCurrency;

    public function __construct(Carbon $from, Carbon $to, string $selectedCurrency = '')
    {
        $this->from = $from->copy();
        $this->to = $to->copy();
        $this->selectedCurrency = strtoupper(trim($selectedCurrency));
    }

    public function title(): string
    {
        return 'Maintenance Expenses';
    }

    public function headings(): array
    {
        return [
            'Expense Date',
            'Property',
            'Unit #',
            'Category',
            'Vendor',
            'Description',
            'Currency',
            'Amount',
        ];
    }

    public function collection(): Collection
    {
        $expenses = MaintenanceExpense::query()
            ->with([
                'maintenanceRequest.unit.property',
                'maintenanceRequest.unit',
            ])
            ->whereBetween('expense_date', [
                $this->from->toDateString(),
                $this->to->toDateString(),
            ])
            ->when($this->selectedCurrency !== '', function ($query) {
                $query->where('currency', $this->selectedCurrency);
            })
            ->orderBy('expense_date')
            ->orderBy('id')
            ->get();

        $rows = collect();
        $totalExpense = 0.0;

        foreach ($expenses as $expense) {
            $curr = strtoupper(trim((string) $expense->currency));

            if (!in_array($curr, Currency::SUPPORTED, true)) {
                continue;
            }

            $amount = round((float) $expense->amount, 2);
            $totalExpense += $amount;

            $rows->push([
                $expense->expense_date ? Carbon::parse($expense->expense_date)->format('Y-m-d') : '',
                $expense->maintenanceRequest?->unit?->property?->name ?? 'N/A',
                $expense->maintenanceRequest?->unit?->unit_number ?? 'N/A',
                $expense->category ?? 'General',
                $expense->vendor_name ?? 'N/A',
                $expense->description ?? '',
                $curr,
                $amount,
            ]);
        }

        // Add TOTAL row
        $rows->push([
            'TOTAL',
            '',
            '',
            '',
            '',
            '',
            $this->selectedCurrency ?: 'ALL',
            round($totalExpense, 2),
        ]);

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:H1')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle('A1:H1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('1E293B');

        // Total row styling
        $lastRow = $sheet->getHighestRow();
        if ($lastRow > 1) {
            $sheet->getStyle("A{$lastRow}:H{$lastRow}")->getFont()->setBold(true);
            $sheet->getStyle("A{$lastRow}:H{$lastRow}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("A{$lastRow}:H{$lastRow}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);
        }

        return [];
    }
}
