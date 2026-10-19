<?php

namespace App\Exports\Sheets;

use App\Models\Payment;
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

class PaymentsSheetExport implements
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
        return 'Payments';
    }

    public function headings(): array
    {
        return [
            'Payment Date',
            'Invoice #',
            'Tenant Name',
            'Property',
            'Unit #',
            'Payment Method',
            'Currency',
            'Amount Paid',
            'Status',
        ];
    }

    public function collection(): Collection
    {
        $payments = Payment::query()
            ->with([
                'rentInvoice.lease.tenant',
                'rentInvoice.lease.unit.property',
                'rentInvoice.lease.unit',
            ])
            ->whereBetween('payment_date', [
                $this->from->toDateString(),
                $this->to->toDateString(),
            ])
            ->where('status', Payment::STATUS_COMPLETED)
            ->when($this->selectedCurrency !== '', function ($query) {
                $query->where('currency', $this->selectedCurrency);
            })
            ->orderBy('payment_date')
            ->orderBy('id')
            ->get();

        $rows = collect();
        $totalPaid = 0.0;

        foreach ($payments as $payment) {
            $curr = strtoupper(trim((string) $payment->currency));

            if (!in_array($curr, Currency::SUPPORTED, true)) {
                continue;
            }

            $paymentMethod = $payment->payment_method
                ? ucwords(str_replace('_', ' ', $payment->payment_method))
                : 'Manual';

            $amount = round((float) $payment->amount, 2);
            $totalPaid += $amount;

            $rows->push([
                $payment->payment_date ? Carbon::parse($payment->payment_date)->format('Y-m-d') : '',
                $payment->rentInvoice?->invoice_number ?? 'N/A',
                $payment->rentInvoice?->lease?->tenant?->name ?? 'N/A',
                $payment->rentInvoice?->lease?->unit?->property?->name ?? 'N/A',
                $payment->rentInvoice?->lease?->unit?->unit_number ?? 'N/A',
                $paymentMethod,
                $curr,
                $amount,
                ucfirst((string) ($payment->status ?? '')),
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
            round($totalPaid, 2),
            '',
        ]);

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:I1')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle('A1:I1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('1E293B');

        // Total row styling
        $lastRow = $sheet->getHighestRow();
        if ($lastRow > 1) {
            $sheet->getStyle("A{$lastRow}:I{$lastRow}")->getFont()->setBold(true);
            $sheet->getStyle("A{$lastRow}:I{$lastRow}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("A{$lastRow}:I{$lastRow}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOUBLE);
        }

        return [];
    }
}
