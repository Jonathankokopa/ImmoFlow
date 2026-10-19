<?php

namespace App\Exports\Sheets;

use App\Models\MaintenanceExpense;
use App\Models\Payment;
use App\Models\RentInvoice;
use App\Models\Unit;
use App\Support\Currency;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SummarySheetExport implements
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
        return 'Summary';
    }

    public function collection(): Collection
    {
        $rows = collect();

        $rows->push(['REAL ESTATE MANAGEMENT SYSTEM - FINANCIAL & OPERATIONAL REPORT']);
        $rows->push(['Period: ' . $this->from->format('d M Y') . ' to ' . $this->to->format('d M Y') . ' | Currency Scope: ' . ($this->selectedCurrency ?: 'All Currencies')]);
        $rows->push(['']);

        $rows->push(['FINANCIAL OVERVIEW BY CURRENCY']);
        $rows->push(['Currency', 'Total Invoiced', 'Total Collected', 'Outstanding Balance', 'Maintenance Expenses', 'Net Position', 'Collection Rate (%)']);

        $currencies = $this->selectedCurrency !== '' ? [$this->selectedCurrency] : Currency::SUPPORTED;

        foreach ($currencies as $currency) {
            $invoiced = (float) RentInvoice::whereBetween('invoice_date', [$this->from->toDateString(), $this->to->toDateString()])
                ->where('currency', $currency)
                ->sum('amount');

            $collected = (float) Payment::whereBetween('payment_date', [$this->from->toDateString(), $this->to->toDateString()])
                ->where('status', Payment::STATUS_COMPLETED)
                ->where('currency', $currency)
                ->sum('amount');

            $invoices = RentInvoice::whereBetween('invoice_date', [$this->from->toDateString(), $this->to->toDateString()])
                ->where('currency', $currency)
                ->get();

            $outstanding = (float) $invoices->sum(function ($inv) {
                return max(0, (float) $inv->outstanding_amount);
            });

            $maintenance = (float) MaintenanceExpense::whereBetween('expense_date', [$this->from->toDateString(), $this->to->toDateString()])
                ->where('currency', $currency)
                ->sum('amount');

            $netPosition = $collected - $maintenance;
            $collectionRate = $invoiced > 0 ? round(($collected / $invoiced) * 100, 2) : 0;

            $rows->push([
                $currency,
                round($invoiced, 2),
                round($collected, 2),
                round($outstanding, 2),
                round($maintenance, 2),
                round($netPosition, 2),
                $collectionRate . '%',
            ]);
        }

        $rows->push(['']);
        $rows->push(['PORTFOLIO OCCUPANCY']);
        $rows->push(['Total Units', 'Occupied Units', 'Vacant Units', 'Occupancy Rate (%)']);

        $totalUnits = Unit::count();
        $occupiedUnits = Unit::where('status', 'occupied')->count();
        $vacantUnits = max(0, $totalUnits - $occupiedUnits);
        $occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100, 1) : 0;

        $rows->push([
            $totalUnits,
            $occupiedUnits,
            $vacantUnits,
            $occupancyRate . '%',
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1E293B'));
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('64748B'));

        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A5:G5')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle('A5:G5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('1E293B');

        $rows = $this->collection();
        $rowCount = count($rows);
        $occHeaderRow = $rowCount - 1;

        $sheet->getStyle("A" . ($occHeaderRow - 1))->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle("A{$occHeaderRow}:D{$occHeaderRow}")->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $sheet->getStyle("A{$occHeaderRow}:D{$occHeaderRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('1E293B');

        return [];
    }
}
