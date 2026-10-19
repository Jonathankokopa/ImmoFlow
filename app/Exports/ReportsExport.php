<?php

namespace App\Exports;

use App\Models\MaintenanceExpense;
use App\Models\Payment;
use App\Models\RentInvoice;
use App\Support\Currency;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportsExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
{
    protected Carbon $from;

    protected Carbon $to;

    protected string $selectedCurrency;

    public function __construct(
        Carbon $from,
        Carbon $to,
        string $selectedCurrency = ''
    ) {
        $this->from = $from->copy();
        $this->to = $to->copy();
        $this->selectedCurrency = strtoupper(
            trim($selectedCurrency)
        );

        if (
            $this->selectedCurrency !== ''
            && !in_array(
                $this->selectedCurrency,
                Currency::SUPPORTED,
                true
            )
        ) {
            $this->selectedCurrency = '';
        }
    }

    /**
     * Build the Excel collection.
     */
    public function collection(): Collection
    {
        $rows = collect();

        $this->addInvoices($rows);
        $this->addPayments($rows);
        $this->addMaintenanceExpenses($rows);

        return $rows
            ->sortBy(function (array $row) {
                return $row['date']
                    . '|'
                    . $this->typeSortOrder($row['type'])
                    . '|'
                    . $row['reference'];
            })
            ->values();
    }

    /**
     * Add invoice records.
     */
    protected function addInvoices(Collection $rows): void
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
            ->when(
                $this->selectedCurrency !== '',
                function ($query) {
                    $query->where(
                        'currency',
                        $this->selectedCurrency
                    );
                }
            )
            ->orderBy('invoice_date')
            ->orderBy('id')
            ->get();

        foreach ($invoices as $invoice) {
            $currency = strtoupper(
                trim((string) $invoice->currency)
            );

            /*
             * Never convert an invalid/missing currency to USD.
             * Invalid historical records are simply excluded
             * from the financial export.
             */
            if (
                !in_array(
                    $currency,
                    Currency::SUPPORTED,
                    true
                )
            ) {
                continue;
            }

            $rows->push([
                'type' => 'Invoice',

                'date' => $invoice->invoice_date
                    ? Carbon::parse(
                        $invoice->invoice_date
                    )->format('Y-m-d')
                    : '',

                'reference' => (string) (
                    $invoice->invoice_number ?? ''
                ),

                'tenant' =>
                    $invoice->lease?->tenant?->name ?? '',

                'property' =>
                    $invoice->lease?->unit?->property?->name ?? '',

                'unit' =>
                    $invoice->lease?->unit?->unit_number ?? '',

                'method' => '',

                'currency' => $currency,

                'amount' => round(
                    (float) $invoice->amount,
                    2
                ),

                'status' => (string) (
                    $invoice->status ?? ''
                ),
            ]);
        }
    }

    /**
     * Add completed payment records.
     */
    protected function addPayments(Collection $rows): void
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
            ->where(
                'status',
                Payment::STATUS_COMPLETED
            )
            ->when(
                $this->selectedCurrency !== '',
                function ($query) {
                    $query->where(
                        'currency',
                        $this->selectedCurrency
                    );
                }
            )
            ->orderBy('payment_date')
            ->orderBy('id')
            ->get();

        foreach ($payments as $payment) {
            $currency = strtoupper(
                trim((string) $payment->currency)
            );

            /*
             * Payment currency is historical and must be
             * preserved exactly as recorded.
             */
            if (
                !in_array(
                    $currency,
                    Currency::SUPPORTED,
                    true
                )
            ) {
                continue;
            }

            $paymentMethod = $payment->payment_method
                ? ucwords(
                    str_replace(
                        '_',
                        ' ',
                        $payment->payment_method
                    )
                )
                : 'Manual';

            $rows->push([
                'type' => 'Payment',

                'date' => $payment->payment_date
                    ? Carbon::parse(
                        $payment->payment_date
                    )->format('Y-m-d')
                    : '',

                'reference' =>
                    $payment->rentInvoice?->invoice_number ?? '',

                'tenant' =>
                    $payment
                        ->rentInvoice
                        ?->lease
                        ?->tenant
                        ?->name ?? '',

                'property' =>
                    $payment
                        ->rentInvoice
                        ?->lease
                        ?->unit
                        ?->property
                        ?->name ?? '',

                'unit' =>
                    $payment
                        ->rentInvoice
                        ?->lease
                        ?->unit
                        ?->unit_number ?? '',

                'method' => $paymentMethod,

                'currency' => $currency,

                'amount' => round(
                    (float) $payment->amount,
                    2
                ),

                'status' => (string) (
                    $payment->status ?? ''
                ),
            ]);
        }
    }

    /**
     * Add maintenance expense records.
     */
    protected function addMaintenanceExpenses(
        Collection $rows
    ): void {
        $maintenanceExpenses = MaintenanceExpense::query()
            ->with([
                'maintenanceRequest.unit.property',
                'maintenanceRequest.unit',
            ])
            ->whereBetween('expense_date', [
                $this->from->toDateString(),
                $this->to->toDateString(),
            ])
            ->when(
                $this->selectedCurrency !== '',
                function ($query) {
                    $query->where(
                        'currency',
                        $this->selectedCurrency
                    );
                }
            )
            ->orderBy('expense_date')
            ->orderBy('id')
            ->get();

        foreach ($maintenanceExpenses as $expense) {
            $currency = strtoupper(
                trim((string) $expense->currency)
            );

            /*
             * Do not silently assign USD to historical
             * maintenance expenses with missing currency.
             */
            if (
                !in_array(
                    $currency,
                    Currency::SUPPORTED,
                    true
                )
            ) {
                continue;
            }

            $rows->push([
                'type' => 'Maintenance',

                'date' => $expense->expense_date
                    ? Carbon::parse(
                        $expense->expense_date
                    )->format('Y-m-d')
                    : '',

                'reference' => '',

                'tenant' => '',

                'property' =>
                    $expense
                        ->maintenanceRequest
                        ?->unit
                        ?->property
                        ?->name ?? '',

                'unit' =>
                    $expense
                        ->maintenanceRequest
                        ?->unit
                        ?->unit_number ?? '',

                'method' => '',

                'currency' => $currency,

                'amount' => round(
                    (float) $expense->amount,
                    2
                ),

                'status' => '',
            ]);
        }
    }

    /**
     * Excel headings.
     */
    public function headings(): array
    {
        return [
            'Record Type',
            'Date',
            'Reference',
            'Tenant',
            'Property',
            'Unit',
            'Payment Method',
            'Currency',
            'Amount',
            'Status',
        ];
    }

    /**
     * Map each row to Excel columns.
     */
    public function map($row): array
    {
        return [
            $row['type'],
            $row['date'],
            $row['reference'],
            $row['tenant'],
            $row['property'],
            $row['unit'],
            $row['method'],
            $row['currency'],
            $row['amount'],
            $row['status'],
        ];
    }

    /**
     * Keep the Excel records consistently ordered.
     */
    protected function typeSortOrder(string $type): int
    {
        return match ($type) {
            'Invoice' => 1,
            'Payment' => 2,
            'Maintenance' => 3,
            default => 99,
        };
    }
}