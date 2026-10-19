<?php

namespace App\Http\Controllers;

use App\Exports\ReportsExport;
use App\Models\Lease;
use App\Models\MaintenanceExpense;
use App\Models\Payment;
use App\Models\RentInvoice;
use App\Models\Unit;
use App\Support\Currency;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Supported reporting currencies.
     */
    protected function currencies(): array
    {
        return Currency::SUPPORTED;
    }

    /**
     * Reports dashboard.
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Reporting Period
        |--------------------------------------------------------------------------
        */

        try {
            $from = $request->filled('from')
                ? Carbon::parse(
                    $request->input('from')
                )->startOfDay()
                : now()->startOfMonth()->startOfDay();

            $to = $request->filled('to')
                ? Carbon::parse(
                    $request->input('to')
                )->endOfDay()
                : now()->endOfMonth()->endOfDay();
        } catch (\Throwable $e) {
            $from = now()->startOfMonth()->startOfDay();
            $to = now()->endOfMonth()->endOfDay();
        }

        /*
        |--------------------------------------------------------------------------
        | Correct Reversed Dates
        |--------------------------------------------------------------------------
        */

        if ($from->gt($to)) {
            [$from, $to] = [
                $to->copy()->startOfDay(),
                $from->copy()->endOfDay(),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Currency Filter
        |--------------------------------------------------------------------------
        */

        $selectedCurrency = strtoupper(
            trim(
                (string) $request->input(
                    'currency',
                    ''
                )
            )
        );

        if (
            $selectedCurrency !== ''
            && !in_array(
                $selectedCurrency,
                $this->currencies(),
                true
            )
        ) {
            $selectedCurrency = '';
        }

        /*
        |--------------------------------------------------------------------------
        | Invoices
        |--------------------------------------------------------------------------
        */

        $invoices = RentInvoice::query()
            ->with([
                'lease.tenant',
                'lease.unit.property',
                'lease.unit',
            ])
            ->whereBetween(
                'invoice_date',
                [
                    $from->toDateString(),
                    $to->toDateString(),
                ]
            )
            ->when(
                $selectedCurrency !== '',
                function ($query) use ($selectedCurrency) {
                    $query->where(
                        'currency',
                        $selectedCurrency
                    );
                }
            )
            ->orderBy('invoice_date')
            ->orderBy('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Completed Payments
        |--------------------------------------------------------------------------
        */

        $payments = Payment::query()
            ->with([
                'rentInvoice.lease.tenant',
                'rentInvoice.lease.unit.property',
                'rentInvoice.lease.unit',
            ])
            ->whereBetween(
                'payment_date',
                [
                    $from->toDateString(),
                    $to->toDateString(),
                ]
            )
            ->where(
                'status',
                Payment::STATUS_COMPLETED
            )
            ->when(
                $selectedCurrency !== '',
                function ($query) use ($selectedCurrency) {
                    $query->where(
                        'currency',
                        $selectedCurrency
                    );
                }
            )
            ->latest('payment_date')
            ->latest('created_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Maintenance Expenses
        |--------------------------------------------------------------------------
        */

        $maintenanceExpenses = MaintenanceExpense::query()
            ->with([
                'maintenanceRequest.unit.property',
                'maintenanceRequest.unit',
            ])
            ->whereBetween(
                'expense_date',
                [
                    $from->toDateString(),
                    $to->toDateString(),
                ]
            )
            ->when(
                $selectedCurrency !== '',
                function ($query) use ($selectedCurrency) {
                    $query->where(
                        'currency',
                        $selectedCurrency
                    );
                }
            )
            ->latest('expense_date')
            ->latest('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Financial Summary By Currency
        |--------------------------------------------------------------------------
        */

        $financialByCurrency = [];

        foreach ($this->currencies() as $currency) {
            $currencyInvoices = $invoices->filter(
                function ($invoice) use ($currency) {
                    return strtoupper(
                        trim((string) $invoice->currency)
                    ) === $currency;
                }
            );

            $currencyPayments = $payments->filter(
                function ($payment) use ($currency) {
                    return strtoupper(
                        trim((string) $payment->currency)
                    ) === $currency;
                }
            );

            $currencyMaintenance = $maintenanceExpenses->filter(
                function ($expense) use ($currency) {
                    return strtoupper(
                        trim((string) $expense->currency)
                    ) === $currency;
                }
            );

            $invoiced = round(
                (float) $currencyInvoices->sum('amount'),
                2
            );

            $collected = round(
                (float) $currencyPayments->sum('amount'),
                2
            );

            $outstanding = round(
                (float) $currencyInvoices->sum(
                    function ($invoice) {
                        return max(
                            0,
                            (float) $invoice->outstanding_amount
                        );
                    }
                ),
                2
            );

            $maintenance = round(
                (float) $currencyMaintenance->sum('amount'),
                2
            );

            $netPosition = round(
                $collected - $maintenance,
                2
            );

            $collectionRate = $invoiced > 0
                ? round(
                    ($collected / $invoiced) * 100,
                    2
                )
                : 0;

            $financialByCurrency[$currency] = [
                'invoice_count' =>
                    $currencyInvoices->count(),

                'payment_count' =>
                    $currencyPayments->count(),

                'maintenance_count' =>
                    $currencyMaintenance->count(),

                'invoiced' =>
                    $invoiced,

                'collected' =>
                    $collected,

                'outstanding' =>
                    $outstanding,

                'maintenance' =>
                    $maintenance,

                'net_position' =>
                    $netPosition,

                'collection_rate' =>
                    $collectionRate,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Monthly Financial Report
        |--------------------------------------------------------------------------
        */

        $monthlyFinancial = $this->buildMonthlyFinancial(
            $from,
            $to,
            $selectedCurrency
        );

        /*
        |--------------------------------------------------------------------------
        | Chart Data
        |--------------------------------------------------------------------------
        */

        $monthlyLabels = [];

        $monthlyChartData = [];

        foreach ($monthlyFinancial as $month) {
            $monthlyLabels[] = $month['label'];

            foreach ($this->currencies() as $currency) {
                if (!isset($monthlyChartData[$currency])) {
                    $monthlyChartData[$currency] = [
                        'invoiced' => [],
                        'collected' => [],
                        'maintenance' => [],
                        'outstanding' => [],
                    ];
                }

                $monthly = $month['currencies'][$currency];

                $monthlyChartData[$currency]['invoiced'][] =
                    $monthly['invoiced'];

                $monthlyChartData[$currency]['collected'][] =
                    $monthly['collected'];

                $monthlyChartData[$currency]['maintenance'][] =
                    $monthly['maintenance'];

                $monthlyChartData[$currency]['outstanding'][] =
                    $monthly['outstanding'];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Payment Method Breakdown
        |--------------------------------------------------------------------------
        */

        $methodBreakdown = $payments
            ->groupBy(
                function ($payment) {
                    return $payment->payment_method
                        ?: 'manual';
                }
            )
            ->map(
                function ($items) {
                    return $items->count();
                }
            );

        $methodLabels = $methodBreakdown
            ->keys()
            ->map(
                function ($method) {
                    return ucwords(
                        str_replace(
                            '_',
                            ' ',
                            $method
                        )
                    );
                }
            )
            ->values();

        $methodValues = $methodBreakdown->values();

        /*
        |--------------------------------------------------------------------------
        | Occupancy
        |--------------------------------------------------------------------------
        */

        $totalUnits = Unit::count();

        $occupiedUnits = Unit::query()
            ->where(
                'status',
                'occupied'
            )
            ->count();

        $vacantUnits = max(
            0,
            $totalUnits - $occupiedUnits
        );

        $occupancyRate = $totalUnits > 0
            ? round(
                ($occupiedUnits / $totalUnits) * 100,
                1
            )
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Active Leases
        |--------------------------------------------------------------------------
        */

        $activeLeases = Lease::query()
            ->with([
                'tenant',
                'unit.property',
                'unit',
            ])
            ->where(
                'status',
                'active'
            )
            ->orderBy('end_date')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Expiring Leases
        |--------------------------------------------------------------------------
        */

        $expiringLeases = $activeLeases
            ->filter(
                function ($lease) {
                    if (!$lease->end_date) {
                        return false;
                    }

                    return Carbon::parse(
                        $lease->end_date
                    )->between(
                        now()->startOfDay(),
                        now()
                            ->copy()
                            ->addDays(30)
                            ->endOfDay()
                    );
                }
            )
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Period Label
        |--------------------------------------------------------------------------
        */

        $periodLabel =
            $from->format('d M Y')
            . ' — '
            . $to->format('d M Y');

        /*
        |--------------------------------------------------------------------------
        | Export Query
        |--------------------------------------------------------------------------
        */

        $exportQuery = http_build_query([
            'from' =>
                $from->toDateString(),

            'to' =>
                $to->toDateString(),

            'currency' =>
                $selectedCurrency,
        ]);

        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        return view(
            'reports.index',
            [
                'from' =>
                    $from,

                'to' =>
                    $to,

                'periodLabel' =>
                    $periodLabel,

                'selectedCurrency' =>
                    $selectedCurrency,

                'financialByCurrency' =>
                    $financialByCurrency,

                'monthlyFinancial' =>
                    $monthlyFinancial,

                'monthlyLabels' =>
                    $monthlyLabels,

                'monthlyChartData' =>
                    $monthlyChartData,

                'invoices' =>
                    $invoices,

                'payments' =>
                    $payments,

                'maintenanceExpenses' =>
                    $maintenanceExpenses,

                'activeLeases' =>
                    $activeLeases,

                'expiringLeases' =>
                    $expiringLeases,

                'methodLabels' =>
                    $methodLabels,

                'methodValues' =>
                    $methodValues,

                'totalUnits' =>
                    $totalUnits,

                'occupiedUnits' =>
                    $occupiedUnits,

                'vacantUnits' =>
                    $vacantUnits,

                'occupancyRate' =>
                    $occupancyRate,

                'exportQuery' =>
                    $exportQuery,
            ]
        );
    }

    /**
     * Build month-by-month financial data.
     */
    protected function buildMonthlyFinancial(
        Carbon $from,
        Carbon $to,
        string $selectedCurrency = ''
    ): array {
        $months = [];

        $cursor = $from
            ->copy()
            ->startOfMonth();

        $lastMonth = $to
            ->copy()
            ->startOfMonth();

        while ($cursor->lte($lastMonth)) {
            $monthStart = $cursor
                ->copy()
                ->startOfMonth();

            $monthEnd = $cursor
                ->copy()
                ->endOfMonth();

            /*
            |--------------------------------------------------------------------------
            | Monthly Invoices
            |--------------------------------------------------------------------------
            */

            $monthInvoices = RentInvoice::query()
                ->whereBetween(
                    'invoice_date',
                    [
                        $monthStart->toDateString(),
                        $monthEnd->toDateString(),
                    ]
                )
                ->when(
                    $selectedCurrency !== '',
                    function ($query) use ($selectedCurrency) {
                        $query->where(
                            'currency',
                            $selectedCurrency
                        );
                    }
                )
                ->get();

            /*
            |--------------------------------------------------------------------------
            | Monthly Payments
            |--------------------------------------------------------------------------
            */

            $monthPayments = Payment::query()
                ->whereBetween(
                    'payment_date',
                    [
                        $monthStart->toDateString(),
                        $monthEnd->toDateString(),
                    ]
                )
                ->where(
                    'status',
                    Payment::STATUS_COMPLETED
                )
                ->when(
                    $selectedCurrency !== '',
                    function ($query) use ($selectedCurrency) {
                        $query->where(
                            'currency',
                            $selectedCurrency
                        );
                    }
                )
                ->get();

            /*
            |--------------------------------------------------------------------------
            | Monthly Maintenance
            |--------------------------------------------------------------------------
            */

            $monthMaintenance = MaintenanceExpense::query()
                ->whereBetween(
                    'expense_date',
                    [
                        $monthStart->toDateString(),
                        $monthEnd->toDateString(),
                    ]
                )
                ->when(
                    $selectedCurrency !== '',
                    function ($query) use ($selectedCurrency) {
                        $query->where(
                            'currency',
                            $selectedCurrency
                        );
                    }
                )
                ->get();

            $currencyData = [];

            /*
            |--------------------------------------------------------------------------
            | Calculate Each Currency Separately
            |--------------------------------------------------------------------------
            */

            foreach ($this->currencies() as $currency) {
                $currencyInvoices = $monthInvoices->filter(
                    function ($invoice) use ($currency) {
                        return strtoupper(
                            trim((string) $invoice->currency)
                        ) === $currency;
                    }
                );

                $currencyPayments = $monthPayments->filter(
                    function ($payment) use ($currency) {
                        return strtoupper(
                            trim((string) $payment->currency)
                        ) === $currency;
                    }
                );

                $currencyMaintenance = $monthMaintenance->filter(
                    function ($expense) use ($currency) {
                        return strtoupper(
                            trim((string) $expense->currency)
                        ) === $currency;
                    }
                );

                $invoiced = round(
                    (float) $currencyInvoices->sum('amount'),
                    2
                );

                $collected = round(
                    (float) $currencyPayments->sum('amount'),
                    2
                );

                $outstanding = round(
                    (float) $currencyInvoices->sum(
                        function ($invoice) {
                            return max(
                                0,
                                (float) $invoice->outstanding_amount
                            );
                        }
                    ),
                    2
                );

                $maintenance = round(
                    (float) $currencyMaintenance->sum('amount'),
                    2
                );

                $netPosition = round(
                    $collected - $maintenance,
                    2
                );

                $collectionRate = $invoiced > 0
                    ? round(
                        ($collected / $invoiced) * 100,
                        2
                    )
                    : 0;

                $currencyData[$currency] = [
                    'invoice_count' =>
                        $currencyInvoices->count(),

                    'payment_count' =>
                        $currencyPayments->count(),

                    'maintenance_count' =>
                        $currencyMaintenance->count(),

                    'invoiced' =>
                        $invoiced,

                    'collected' =>
                        $collected,

                    'outstanding' =>
                        $outstanding,

                    'maintenance' =>
                        $maintenance,

                    'net_position' =>
                        $netPosition,

                    'collection_rate' =>
                        $collectionRate,
                ];
            }

            $months[] = [
                'key' =>
                    $cursor->format('Y-m'),

                'label' =>
                    $cursor->format('F Y'),

                'currencies' =>
                    $currencyData,
            ];

            $cursor->addMonth();
        }

        return $months;
    }

    /**
     * Export report to Excel.
     */
    public function export(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Reporting Period
        |--------------------------------------------------------------------------
        */

        try {
            $from = $request->filled('from')
                ? Carbon::parse(
                    $request->input('from')
                )->startOfDay()
                : now()->startOfMonth()->startOfDay();

            $to = $request->filled('to')
                ? Carbon::parse(
                    $request->input('to')
                )->endOfDay()
                : now()->endOfMonth()->endOfDay();
        } catch (\Throwable $e) {
            $from = now()
                ->startOfMonth()
                ->startOfDay();

            $to = now()
                ->endOfMonth()
                ->endOfDay();
        }

        /*
        |--------------------------------------------------------------------------
        | Correct Reversed Dates
        |--------------------------------------------------------------------------
        */

        if ($from->gt($to)) {
            [$from, $to] = [
                $to->copy()->startOfDay(),
                $from->copy()->endOfDay(),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Currency
        |--------------------------------------------------------------------------
        */

        $currency = strtoupper(
            trim(
                (string) $request->input(
                    'currency',
                    ''
                )
            )
        );

        if (
            $currency !== ''
            && !in_array(
                $currency,
                $this->currencies(),
                true
            )
        ) {
            $currency = '';
        }

        /*
        |--------------------------------------------------------------------------
        | Filename
        |--------------------------------------------------------------------------
        */

        $filename =
            'property-report-'
            . $from->format('Y-m-d')
            . '-to-'
            . $to->format('Y-m-d');

        if ($currency !== '') {
            $filename .= '-'
                . strtolower($currency);
        }

        $filename .= '.xlsx';

        /*
        |--------------------------------------------------------------------------
        | Excel Download
        |--------------------------------------------------------------------------
        |
        | Laravel Excel generates the correct XLSX content type
        | and attachment headers automatically.
        |
        */

        return Excel::download(
            new ReportsExport(
                $from,
                $to,
                $currency
            ),
            $filename,
            \Maatwebsite\Excel\Excel::XLSX
        );
    }
}