<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\MaintenanceExpense;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RentInvoice;
use App\Models\Tenant;
use App\Models\Unit;
use App\Support\Currency;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $user = $request->user();

        if ($user->isTenant()) {
            return redirect()->route('portal');
        }

        if ($user->isOwner()) {
            return redirect()->route('owner.dashboard');
        }

        $now = Carbon::now();
        $selectedOwnerId = $request->query('owner_id');
        $selectedPropertyId = $request->query('property_id');

        // Owners list for filter dropdown (if Admin)
        $ownersList = $user->isAdmin() ? \App\Models\Owner::orderBy('full_name')->get() : collect();

        // Properties list for filter dropdown
        $filterPropertiesQuery = Property::forUser();
        if ($selectedOwnerId) {
            $filterPropertiesQuery->where('owner_id', $selectedOwnerId);
        }
        $filterPropertiesList = $filterPropertiesQuery->orderBy('name')->get();

        /*
        |--------------------------------------------------------------------------
        | Base Scoped Queries
        |--------------------------------------------------------------------------
        */

        $propertiesQuery = Property::forUser();
        $unitsQuery = Unit::forUser();
        $tenantsQuery = Tenant::forUser();
        $rentInvoicesQuery = RentInvoice::forUser();
        $paymentsQuery = Payment::forUser();
        $maintenanceRequestsQuery = MaintenanceRequest::forUser();
        $maintenanceExpensesQuery = MaintenanceExpense::forUser();
        $leasesQuery = Lease::forUser();

        if ($selectedOwnerId) {
            $propertiesQuery->where('owner_id', $selectedOwnerId);
            $unitsQuery->whereHas('property', fn ($q) => $q->where('owner_id', $selectedOwnerId));
            $tenantsQuery->whereHas('leases.unit.property', fn ($q) => $q->where('owner_id', $selectedOwnerId));
            $rentInvoicesQuery->whereHas('lease.unit.property', fn ($q) => $q->where('owner_id', $selectedOwnerId));
            $paymentsQuery->whereHas('rentInvoice.lease.unit.property', fn ($q) => $q->where('owner_id', $selectedOwnerId));
            $maintenanceRequestsQuery->whereHas('unit.property', fn ($q) => $q->where('owner_id', $selectedOwnerId));
            $maintenanceExpensesQuery->whereHas('maintenanceRequest.unit.property', fn ($q) => $q->where('owner_id', $selectedOwnerId));
            $leasesQuery->whereHas('unit.property', fn ($q) => $q->where('owner_id', $selectedOwnerId));
        }

        if ($selectedPropertyId) {
            $propertiesQuery->where('id', $selectedPropertyId);
            $unitsQuery->where('property_id', $selectedPropertyId);
            $tenantsQuery->whereHas('leases.unit', fn ($q) => $q->where('property_id', $selectedPropertyId));
            $rentInvoicesQuery->whereHas('lease.unit', fn ($q) => $q->where('property_id', $selectedPropertyId));
            $paymentsQuery->whereHas('rentInvoice.lease.unit', fn ($q) => $q->where('property_id', $selectedPropertyId));
            $maintenanceRequestsQuery->whereHas('unit', fn ($q) => $q->where('property_id', $selectedPropertyId));
            $maintenanceExpensesQuery->whereHas('maintenanceRequest.unit', fn ($q) => $q->where('property_id', $selectedPropertyId));
            $leasesQuery->whereHas('unit', fn ($q) => $q->where('property_id', $selectedPropertyId));
        }

        /*
        |--------------------------------------------------------------------------
        | Property Statistics
        |--------------------------------------------------------------------------
        */

        $totalProperties = (clone $propertiesQuery)->count();
        $totalUnits = (clone $unitsQuery)->count();
        $occupiedUnits = (clone $unitsQuery)->where('status', 'occupied')->count();
        $vacantUnits = (clone $unitsQuery)->where('status', 'vacant')->count();

        $occupancyRate = $totalUnits > 0
            ? round(($occupiedUnits / $totalUnits) * 100, 1)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Tenant Statistics
        |--------------------------------------------------------------------------
        */

        $activeTenants = (clone $tenantsQuery)->where('status', 'active')->count();

        /*
        |--------------------------------------------------------------------------
        | Supported Currencies
        |--------------------------------------------------------------------------
        |
        | All financial calculations are kept separate by currency.
        |
        */

        $supportedCurrencies = Currency::SUPPORTED;

        /*
        |--------------------------------------------------------------------------
        | Finance Statistics By Currency
        |--------------------------------------------------------------------------
        */

        $financialByCurrency = [];

        foreach ($supportedCurrencies as $currency) {
            $financialByCurrency[$currency] = [
                'rent_invoiced' => 0.00,
                'rent_collected' => 0.00,
                'outstanding_rent' => 0.00,
                'maintenance_expenses' => 0.00,
                'net_position' => 0.00,
                'collection_rate' => 0.00,

                'collected_this_month' => 0.00,
                'collected_last_month' => 0.00,

                'invoiced_this_month' => 0.00,
                'invoiced_last_month' => 0.00,

                'collection_rate_this_month' => null,
                'collection_rate_last_month' => null,
                'collection_rate_trend' => null,

                'outstanding_this_month' => 0.00,
                'outstanding_last_month' => 0.00,
                'outstanding_rent_trend' => null,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | All Rent Invoiced By Currency
        |--------------------------------------------------------------------------
        */

        $rentInvoicedByCurrency = (clone $rentInvoicesQuery)
            ->selectRaw('currency, SUM(amount) as total')
            ->where('status', '!=', RentInvoice::STATUS_CANCELLED)
            ->whereIn('currency', $supportedCurrencies)
            ->groupBy('currency')
            ->get();

        foreach ($rentInvoicedByCurrency as $row) {
            $currency = strtoupper((string) $row->currency);

            if (!isset($financialByCurrency[$currency])) {
                continue;
            }

            $financialByCurrency[$currency]['rent_invoiced'] = round(
                (float) $row->total,
                2
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Completed Rent Collected By Currency
        |--------------------------------------------------------------------------
        |
        | Only completed payments count as collected rent.
        |
        */

        $rentCollectedByCurrency = (clone $paymentsQuery)
            ->selectRaw('currency, SUM(amount) as total')
            ->where('status', Payment::STATUS_COMPLETED)
            ->whereIn('currency', $supportedCurrencies)
            ->groupBy('currency')
            ->get();

        foreach ($rentCollectedByCurrency as $row) {
            $currency = strtoupper((string) $row->currency);

            if (!isset($financialByCurrency[$currency])) {
                continue;
            }

            $financialByCurrency[$currency]['rent_collected'] = round(
                (float) $row->total,
                2
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Maintenance Expenses By Currency
        |--------------------------------------------------------------------------
        */

        $maintenanceExpensesByCurrency = (clone $maintenanceExpensesQuery)
            ->selectRaw('currency, SUM(amount) as total')
            ->whereIn('currency', $supportedCurrencies)
            ->groupBy('currency')
            ->get();

        foreach ($maintenanceExpensesByCurrency as $row) {
            $currency = strtoupper((string) $row->currency);

            if (!isset($financialByCurrency[$currency])) {
                continue;
            }

            $financialByCurrency[$currency]['maintenance_expenses'] = round(
                (float) $row->total,
                2
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate Current Financial Position
        |--------------------------------------------------------------------------
        */

        foreach ($supportedCurrencies as $currency) {
            $invoiced = $financialByCurrency[$currency]['rent_invoiced'];
            $collected = $financialByCurrency[$currency]['rent_collected'];
            $expenses = $financialByCurrency[$currency]['maintenance_expenses'];

            $financialByCurrency[$currency]['outstanding_rent'] = round(
                max(0, $invoiced - $collected),
                2
            );

            $financialByCurrency[$currency]['collection_rate'] = $invoiced > 0
                ? round(($collected / $invoiced) * 100, 1)
                : 0;

            $financialByCurrency[$currency]['net_position'] = round(
                $collected - $expenses,
                2
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Operational & Financial Metrics (Today, Partially Paid, Overdue)
        |--------------------------------------------------------------------------
        */

        $todayStr = $now->toDateString();

        foreach ($supportedCurrencies as $currency) {
            $financialByCurrency[$currency]['collected_today'] = 0.00;
            $financialByCurrency[$currency]['partially_paid_count'] = 0;
            $financialByCurrency[$currency]['partially_paid_amount'] = 0.00;
            $financialByCurrency[$currency]['overdue_count'] = 0;
            $financialByCurrency[$currency]['overdue_amount'] = 0.00;
        }

        // Rent Collected Today
        $collectedTodayByCurrency = (clone $paymentsQuery)
            ->selectRaw('currency, SUM(amount) as total')
            ->where('status', Payment::STATUS_COMPLETED)
            ->whereIn('currency', $supportedCurrencies)
            ->whereDate('payment_date', $todayStr)
            ->groupBy('currency')
            ->get();

        foreach ($collectedTodayByCurrency as $row) {
            $curr = strtoupper((string) $row->currency);
            if (isset($financialByCurrency[$curr])) {
                $financialByCurrency[$curr]['collected_today'] = round((float) $row->total, 2);
            }
        }

        // Partially Paid Accounts / Invoices
        $partiallyPaidInvoicesGrouped = (clone $rentInvoicesQuery)
            ->selectRaw('currency, COUNT(*) as cnt, SUM(amount - amount_paid) as balance_due')
            ->where('status', RentInvoice::STATUS_PARTIALLY_PAID)
            ->whereIn('currency', $supportedCurrencies)
            ->groupBy('currency')
            ->get();

        $partiallyPaidTotalCount = 0;
        foreach ($partiallyPaidInvoicesGrouped as $row) {
            $curr = strtoupper((string) $row->currency);
            $cnt = (int) $row->cnt;
            $partiallyPaidTotalCount += $cnt;
            if (isset($financialByCurrency[$curr])) {
                $financialByCurrency[$curr]['partially_paid_count'] = $cnt;
                $financialByCurrency[$curr]['partially_paid_amount'] = round((float) $row->balance_due, 2);
            }
        }

        // Overdue Invoices
        $overdueInvoicesCount = (clone $rentInvoicesQuery)
            ->where('status', '!=', RentInvoice::STATUS_CANCELLED)
            ->where('status', '!=', RentInvoice::STATUS_PAID)
            ->whereDate('due_date', '<', $todayStr)
            ->count();

        $overdueInvoicesGrouped = (clone $rentInvoicesQuery)
            ->selectRaw('currency, COUNT(*) as cnt, SUM(amount - amount_paid) as balance_due')
            ->where('status', '!=', RentInvoice::STATUS_CANCELLED)
            ->where('status', '!=', RentInvoice::STATUS_PAID)
            ->whereDate('due_date', '<', $todayStr)
            ->whereIn('currency', $supportedCurrencies)
            ->groupBy('currency')
            ->get();

        foreach ($overdueInvoicesGrouped as $row) {
            $curr = strtoupper((string) $row->currency);
            if (isset($financialByCurrency[$curr])) {
                $financialByCurrency[$curr]['overdue_count'] = (int) $row->cnt;
                $financialByCurrency[$curr]['overdue_amount'] = round((float) $row->balance_due, 2);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Maintenance Statistics
        |--------------------------------------------------------------------------
        */

        $openMaintenanceRequests = (clone $maintenanceRequestsQuery)
            ->whereIn('status', [
                'pending',
                'assigned',
                'in_progress',
            ])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Lease Renewals
        |--------------------------------------------------------------------------
        |
        | Leases ending within the next 30 days.
        |
        */

        $expiringLeases = (clone $leasesQuery)
            ->with([
                'tenant',
                'unit.property',
            ])
            ->whereBetween('end_date', [
                $now->copy()->startOfDay(),
                $now->copy()->addDays(30)->endOfDay(),
            ])
            ->orderBy('end_date')
            ->limit(6)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Recent Payments
        |--------------------------------------------------------------------------
        */

        $recentPayments = (clone $paymentsQuery)
            ->with([
                'rentInvoice.lease.tenant',
                'rentInvoice.lease.unit.property',
                'recordedBy',
            ])
            ->latest('created_at')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Recent Maintenance Requests
        |--------------------------------------------------------------------------
        */

        $recentMaintenanceRequests = (clone $maintenanceRequestsQuery)
            ->with([
                'unit.property',
            ])
            ->latest('created_at')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Monthly Comparison Periods
        |--------------------------------------------------------------------------
        */

        $startOfThisMonth = $now->copy()->startOfMonth();

        $startOfLastMonth = $now
            ->copy()
            ->subMonthNoOverflow()
            ->startOfMonth();

        /*
        |--------------------------------------------------------------------------
        | Same Day Of Previous Month
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | Today: September 15
        |
        | Current period:
        | September 1 - September 15
        |
        | Previous period:
        | August 1 - August 15
        |
        */

        $endOfLastMonthSamePoint = $startOfLastMonth
            ->copy()
            ->addDays($now->day - 1)
            ->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | Rent Collected Trends By Currency
        |--------------------------------------------------------------------------
        */

        $collectedThisMonthByCurrency = (clone $paymentsQuery)
            ->selectRaw('currency, SUM(amount) as total')
            ->where('status', Payment::STATUS_COMPLETED)
            ->whereIn('currency', $supportedCurrencies)
            ->whereBetween('created_at', [
                $startOfThisMonth,
                $now,
            ])
            ->groupBy('currency')
            ->get();

        foreach ($collectedThisMonthByCurrency as $row) {
            $currency = strtoupper((string) $row->currency);

            if (!isset($financialByCurrency[$currency])) {
                continue;
            }

            $financialByCurrency[$currency]['collected_this_month'] = round(
                (float) $row->total,
                2
            );
        }

        $collectedLastMonthByCurrency = (clone $paymentsQuery)
            ->selectRaw('currency, SUM(amount) as total')
            ->where('status', Payment::STATUS_COMPLETED)
            ->whereIn('currency', $supportedCurrencies)
            ->whereBetween('created_at', [
                $startOfLastMonth,
                $endOfLastMonthSamePoint,
            ])
            ->groupBy('currency')
            ->get();

        foreach ($collectedLastMonthByCurrency as $row) {
            $currency = strtoupper((string) $row->currency);

            if (!isset($financialByCurrency[$currency])) {
                continue;
            }

            $financialByCurrency[$currency]['collected_last_month'] = round(
                (float) $row->total,
                2
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Rent Invoiced Trends By Currency
        |--------------------------------------------------------------------------
        */

        $invoicedThisMonthByCurrency = (clone $rentInvoicesQuery)
            ->selectRaw('currency, SUM(amount) as total')
            ->where('status', '!=', RentInvoice::STATUS_CANCELLED)
            ->whereIn('currency', $supportedCurrencies)
            ->whereBetween('due_date', [
                $startOfThisMonth,
                $now,
            ])
            ->groupBy('currency')
            ->get();

        foreach ($invoicedThisMonthByCurrency as $row) {
            $currency = strtoupper((string) $row->currency);

            if (!isset($financialByCurrency[$currency])) {
                continue;
            }

            $financialByCurrency[$currency]['invoiced_this_month'] = round(
                (float) $row->total,
                2
            );
        }

        $invoicedLastMonthByCurrency = (clone $rentInvoicesQuery)
            ->selectRaw('currency, SUM(amount) as total')
            ->where('status', '!=', RentInvoice::STATUS_CANCELLED)
            ->whereIn('currency', $supportedCurrencies)
            ->whereBetween('due_date', [
                $startOfLastMonth,
                $endOfLastMonthSamePoint,
            ])
            ->groupBy('currency')
            ->get();

        foreach ($invoicedLastMonthByCurrency as $row) {
            $currency = strtoupper((string) $row->currency);

            if (!isset($financialByCurrency[$currency])) {
                continue;
            }

            $financialByCurrency[$currency]['invoiced_last_month'] = round(
                (float) $row->total,
                2
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate Monthly Trends By Currency
        |--------------------------------------------------------------------------
        */

        foreach ($supportedCurrencies as $currency) {
            $invoicedThisMonth =
                $financialByCurrency[$currency]['invoiced_this_month'];

            $collectedThisMonth =
                $financialByCurrency[$currency]['collected_this_month'];

            $invoicedLastMonth =
                $financialByCurrency[$currency]['invoiced_last_month'];

            $collectedLastMonth =
                $financialByCurrency[$currency]['collected_last_month'];

            /*
            |----------------------------------------------------------------------
            | Current Month Collection Rate
            |----------------------------------------------------------------------
            */

            $collectionRateThisMonth = $invoicedThisMonth > 0
                ? ($collectedThisMonth / $invoicedThisMonth) * 100
                : null;

            /*
            |----------------------------------------------------------------------
            | Previous Month Collection Rate
            |----------------------------------------------------------------------
            */

            $collectionRateLastMonth = $invoicedLastMonth > 0
                ? ($collectedLastMonth / $invoicedLastMonth) * 100
                : null;

            /*
            |----------------------------------------------------------------------
            | Collection Rate Trend
            |----------------------------------------------------------------------
            */

            $financialByCurrency[$currency]['collection_rate_this_month'] =
                is_null($collectionRateThisMonth)
                    ? null
                    : round($collectionRateThisMonth, 1);

            $financialByCurrency[$currency]['collection_rate_last_month'] =
                is_null($collectionRateLastMonth)
                    ? null
                    : round($collectionRateLastMonth, 1);

            $financialByCurrency[$currency]['collection_rate_trend'] =
                $this->percentPointChange(
                    $collectionRateThisMonth,
                    $collectionRateLastMonth
                );

            /*
            |----------------------------------------------------------------------
            | Outstanding Rent This Month
            |----------------------------------------------------------------------
            */

            $outstandingThisMonth = max(
                0,
                $invoicedThisMonth - $collectedThisMonth
            );

            $outstandingLastMonth = max(
                0,
                $invoicedLastMonth - $collectedLastMonth
            );

            $financialByCurrency[$currency]['outstanding_this_month'] =
                round($outstandingThisMonth, 2);

            $financialByCurrency[$currency]['outstanding_last_month'] =
                round($outstandingLastMonth, 2);

            /*
            |----------------------------------------------------------------------
            | Outstanding Rent Trend
            |----------------------------------------------------------------------
            */

            $financialByCurrency[$currency]['outstanding_rent_trend'] =
                $this->percentChange(
                    $outstandingThisMonth,
                    $outstandingLastMonth
                );
        }

        /*
        |--------------------------------------------------------------------------
        | 12-Month Past Period Initialization
        |--------------------------------------------------------------------------
        */

        $pastMonths = [];
        $pastMonthLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthDate = $now->copy()->subMonths($i);
            $monthKey = $monthDate->format('Y-m');
            $pastMonths[$monthKey] = [
                'start' => $monthDate->copy()->startOfMonth(),
                'end' => $monthDate->copy()->endOfMonth(),
                'label' => $monthDate->format('M Y'),
            ];
            $pastMonthLabels[] = $monthDate->format('M Y');
        }

        /*
        |--------------------------------------------------------------------------
        | 12-Month Financial Revenue & Invoicing Trends By Currency
        |--------------------------------------------------------------------------
        */

        $monthlyRevenueTrend = [];
        foreach ($supportedCurrencies as $currency) {
            $monthlyRevenueTrend[$currency] = [
                'labels' => $pastMonthLabels,
                'invoiced' => array_fill(0, 12, 0.0),
                'collected' => array_fill(0, 12, 0.0),
            ];
        }

        $twelveStart = reset($pastMonths)['start'];
        $twelveEnd = end($pastMonths)['end'];

        $allPayments12m = (clone $paymentsQuery)
            ->where('status', Payment::STATUS_COMPLETED)
            ->whereIn('currency', $supportedCurrencies)
            ->whereBetween('payment_date', [$twelveStart->toDateString(), $twelveEnd->toDateString()])
            ->get();

        $paymentsGrouped = [];
        foreach ($allPayments12m as $p) {
            $curr = strtoupper((string) $p->currency);
            $mkey = Carbon::parse($p->payment_date)->format('Y-m');
            $paymentsGrouped[$curr][$mkey] = ($paymentsGrouped[$curr][$mkey] ?? 0) + (float) $p->amount;
        }

        $allInvoices12m = (clone $rentInvoicesQuery)
            ->where('status', '!=', RentInvoice::STATUS_CANCELLED)
            ->whereIn('currency', $supportedCurrencies)
            ->whereBetween('due_date', [$twelveStart->toDateString(), $twelveEnd->toDateString()])
            ->get();

        $invoicesGrouped = [];
        foreach ($allInvoices12m as $inv) {
            $curr = strtoupper((string) $inv->currency);
            $mkey = Carbon::parse($inv->due_date)->format('Y-m');
            $invoicesGrouped[$curr][$mkey] = ($invoicesGrouped[$curr][$mkey] ?? 0) + (float) $inv->amount;
        }

        $idx = 0;
        foreach ($pastMonths as $mkey => $mInfo) {
            foreach ($supportedCurrencies as $curr) {
                $monthlyRevenueTrend[$curr]['collected'][$idx] = round($paymentsGrouped[$curr][$mkey] ?? 0.0, 2);
                $monthlyRevenueTrend[$curr]['invoiced'][$idx] = round($invoicesGrouped[$curr][$mkey] ?? 0.0, 2);
            }
            $idx++;
        }

        /*
        |--------------------------------------------------------------------------
        | 12-Month Occupancy Trend
        |--------------------------------------------------------------------------
        */

        $occupancyTrend = [
            'labels' => $pastMonthLabels,
            'rate' => [],
            'occupied' => [],
            'vacant' => [],
        ];

        foreach ($pastMonths as $mkey => $mInfo) {
            $mStart = $mInfo['start'];
            $mEnd = $mInfo['end'];

            $activeLeasesInMonthCount = (clone $leasesQuery)
                ->where('start_date', '<=', $mEnd->toDateString())
                ->where(function ($q) use ($mStart) {
                    $q->whereNull('end_date')
                      ->orWhere('end_date', '>=', $mStart->toDateString());
                })
                ->whereNotIn('status', ['terminated', 'cancelled'])
                ->distinct('unit_id')
                ->count('unit_id');

            $occRate = $totalUnits > 0
                ? round(($activeLeasesInMonthCount / $totalUnits) * 100, 1)
                : 0;

            $occupancyTrend['rate'][] = min(100, max(0, $occRate));
            $occupancyTrend['occupied'][] = min($totalUnits, $activeLeasesInMonthCount);
            $occupancyTrend['vacant'][] = max(0, $totalUnits - $activeLeasesInMonthCount);
        }

        /*
        |--------------------------------------------------------------------------
        | Expense Distribution Breakdown By Currency
        |--------------------------------------------------------------------------
        */

        $expenseDistribution = [];
        foreach ($supportedCurrencies as $currency) {
            $expenseDistribution[$currency] = [
                'labels' => [],
                'values' => [],
            ];
        }

        $expenses = (clone $maintenanceExpensesQuery)
            ->whereIn('currency', $supportedCurrencies)
            ->get();

        $expensesGrouped = [];
        foreach ($expenses as $exp) {
            $curr = strtoupper((string) $exp->currency);
            $type = ucfirst(str_replace('_', ' ', trim((string) ($exp->expense_type ?: 'General'))));
            $expensesGrouped[$curr][$type] = ($expensesGrouped[$curr][$type] ?? 0) + (float) $exp->amount;
        }

        foreach ($supportedCurrencies as $curr) {
            if (isset($expensesGrouped[$curr]) && count($expensesGrouped[$curr]) > 0) {
                foreach ($expensesGrouped[$curr] as $type => $amt) {
                    $expenseDistribution[$curr]['labels'][] = $type;
                    $expenseDistribution[$curr]['values'][] = round($amt, 2);
                }
            } else {
                $expenseDistribution[$curr]['labels'] = ['Maintenance', 'Repairs', 'Utilities', 'General'];
                $expenseDistribution[$curr]['values'] = [0, 0, 0, 0];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        return view('dashboard', [
            'totalProperties' => $totalProperties,
            'totalUnits' => $totalUnits,
            'occupiedUnits' => $occupiedUnits,
            'vacantUnits' => $vacantUnits,
            'occupancyRate' => $occupancyRate,
            'activeTenants' => $activeTenants,
            'financialByCurrency' => $financialByCurrency,
            'supportedCurrencies' => $supportedCurrencies,
            'overdueInvoicesCount' => $overdueInvoicesCount,
            'partiallyPaidTotalCount' => $partiallyPaidTotalCount,
            'openMaintenanceRequests' => $openMaintenanceRequests,
            'expiringLeases' => $expiringLeases,
            'recentPayments' => $recentPayments,
            'recentMaintenanceRequests' => $recentMaintenanceRequests,
            'monthlyRevenueTrend' => $monthlyRevenueTrend,
            'occupancyTrend' => $occupancyTrend,
            'expenseDistribution' => $expenseDistribution,

            'ownersList' => $ownersList,
            'filterPropertiesList' => $filterPropertiesList,
            'selectedOwnerId' => $selectedOwnerId,
            'selectedPropertyId' => $selectedPropertyId,
        ]);
    }

    /**
     * Calculate signed percentage change between two absolute values.
     *
     * Example:
     *
     * Previous = 100
     * Current  = 120
     * Result   = +20.0
     */
    private function percentChange(
        float $current,
        float $previous
    ): ?float {
        if ($previous <= 0) {
            return null;
        }

        return round(
            (($current - $previous) / $previous) * 100,
            1
        );
    }

    /**
     * Calculate percentage-point difference.
     *
     * Example:
     *
     * Current collection rate  = 82%
     * Previous collection rate = 76%
     * Result                   = +6.0
     */
    private function percentPointChange(
        ?float $current,
        ?float $previous
    ): ?float {
        if (
            is_null($current) ||
            is_null($previous)
        ) {
            return null;
        }

        return round(
            $current - $previous,
            1
        );
    }
}