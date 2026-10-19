<?php

namespace App\Exports\Sheets;

use App\Models\RentInvoice;
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

class InvoicesSheetExport implements
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
        return 'Invoices';
    }

    public function headings(): array
    {
        return [
            'Invoice #',
            'Invoice Date',
            'Due Date',
            'Tenant Name',
            'Property',
            'Unit #',
            'Currency',
            'Amount',
            'Outstanding',
            'Status',
        ];
    }

    public function collection(): Collection
    {
        $invoices = RentInvoice::query()
            ->with([
                'lease.tenant',
                'lease.unit.property',
                'lease.unit',
            ])
            ->whereBetween('invoice_date', [
                $this->from->toDateString(),
                $this->to->toDateString(),
            ])
            ->when($this->selectedCurrency !== '', function ($query) {
                $query->where('currency', $this->selectedCurrency);
            })
            ->orderBy('invoice_date')
            ->orderBy('id')
            ->get();

        $rows = collect();
        $totalAmount = 0.0;
        $totalOutstanding = 0.0;

        foreach ($invoices as $invoice) {
            $curr = strtoupper(trim((string) $invoice->currency));

            if (!in_array($curr, Currency::SUPPORTED, true)) {
                continue;
            }

            $amount = round((float) $invoice->amount, 2);
            $outstanding = max(0, round((float) $invoice->outstanding_amount, 2));

            $totalAmount += $amount;
            $totalOutstanding += $outstanding;

            $rows->push([
                (string) ($invoice->invoice_number ?? ''),
                $invoice->invoice_date ? Carbon::parse($invoice->invoice_date)->format('Y-m-d') : '',
                $invoice->due_date ? Carbon::parse($invoice->due_date)->format('Y-m-d') : '',
                $invoice->lease?->tenant?->name ?? 'N/A',
                $invoice->lease?->unit?->property?->name ?? 'N/A',
                $invoice->lease?->unit?->unit_number ?? 'N/A',
                $curr,
                $amount,
                $outstanding,
                ucfirst((string) ($invoice->status ?? '')),
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
            round($totalAmount, 2),
            round($totalOutstanding, 2),
            '',
        ]);

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:J1')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle('A1:J1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('1E293B');

        // Total row styling
        $lastRow = $sheet->getHighestRow();
        if ($lastRow > 1) {
            $sheet->getStyle("A{$lastRow}:J{$lastRow}")->getFont()->setBold(true);
            $sheet->getStyle("A{$lastRow}:J{$lastRow}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("A{$lastRow}:J{$lastRow}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);
        }

        return [];
    }
}
