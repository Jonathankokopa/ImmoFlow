<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\MaintenanceExpense;
use App\Models\MaintenanceRequest;
use App\Models\Owner;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RentInvoice;
use App\Models\Tenant;
use App\Models\Unit;
use App\Support\Currency;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OwnerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Determine target owner:
        // If user is owner or staff bound to an owner, use their owner_id.
        // If user is admin, allow selecting owner_id via query parameter (defaults to first owner if none provided).
        $ownerId = null;
        if ($user->isOwner() || ($user->isStaff() && $user->owner_id)) {
            $ownerId = $user->owner_id;
        } elseif ($user->isAdmin()) {
            $ownerId = $request->query('owner_id', Owner::first()?->id);
        }

        $owner = $ownerId ? Owner::find($ownerId) : null;
        $ownersList = $user->isAdmin() ? Owner::orderBy('full_name')->get() : collect();

        /*
        |--------------------------------------------------------------------------
        | Timeframe Filter Logic
        |--------------------------------------------------------------------------
        */

        $timeframe = $request->query('timeframe', 'this_month');
        $now = Carbon::now();

        switch ($timeframe) {
            case 'today':
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'this_week':
                $startDate = $now->copy()->startOfWeek();
                $endDate = $now->copy()->endOfWeek();
                break;
            case 'last_month':
                $startDate = $now->copy()->subMonthNoOverflow()->startOfMonth();
                $endDate = $now->copy()->subMonthNoOverflow()->endOfMonth();
                break;
            case 'this_quarter':
                $startDate = $now->copy()->startOfQuarter();
                $endDate = $now->copy()->endOfQuarter();
                break;
            case 'this_year':
                $startDate = $now->copy()->startOfYear();
                $endDate = $now->copy()->endOfYear();
                break;
            case 'custom':
                $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date'))->startOfDay() : $now->copy()->startOfMonth();
                $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : $now->copy()->endOfMonth();
                break;
            case 'this_month':
            default:
                $timeframe = 'this_month';
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Base Scoped Queries for Target Owner
        |--------------------------------------------------------------------------
        */

        $propertiesQuery = Property::query();
        $unitsQuery = Unit::query();
        $tenantsQuery = Tenant::query();
        $rentInvoicesQuery = RentInvoice::query();
        $paymentsQuery = Payment::query();
        $maintenanceRequestsQuery = MaintenanceRequest::query();
        $maintenanceExpensesQuery = MaintenanceExpense::query();
        $leasesQuery = Lease::query();

        if ($ownerId) {
            $propertiesQuery->where('owner_id', $ownerId);
            $unitsQuery->whereHas('property', fn ($q) => $q->where('owner_id', $ownerId));
            $tenantsQuery->whereHas('leases.unit.property', fn ($q) => $q->where('owner_id', $ownerId));
            $rentInvoicesQuery->whereHas('lease.unit.property', fn ($q) => $q->where('owner_id', $ownerId));
            $paymentsQuery->whereHas('rentInvoice.lease.unit.property', fn ($q) => $q->where('owner_id', $ownerId));
            $maintenanceRequestsQuery->whereHas('unit.property', fn ($q) => $q->where('owner_id', $ownerId));
            $maintenanceExpensesQuery->whereHas('maintenanceRequest.unit.property', fn ($q) => $q->where('owner_id', $ownerId));
            $leasesQuery->whereHas('unit.property', fn ($q) => $q->where('owner_id', $ownerId));
        }

        /*
        |--------------------------------------------------------------------------
        | Portfolio KPIs
        |--------------------------------------------------------------------------
        */

        $totalProperties = (clone $propertiesQuery)->count();
        $totalUnits = (clone $unitsQuery)->count();
        $occupiedUnits = (clone $unitsQuery)->where('status', 'occupied')->count();
        $vacantUnits = (clone $unitsQuery)->where('status', 'vacant')->count();

        $occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100, 1) : 0;
        $activeTenants = (clone $tenantsQuery)->where('status', 'active')->count();

        /*
        |--------------------------------------------------------------------------
        | Financial Analytics By Currency
        |--------------------------------------------------------------------------
        */

        $supportedCurrencies = Currency::SUPPORTED;
        $financialByCurrency = [];

        foreach ($supportedCurrencies as $currency) {
            $financialByCurrency[$currency] = [
                'expected_rent' => 0.00,
                'collected_rent' => 0.00,
                'outstanding_rent' => 0.00,
                'overdue_rent' => 0.00,
                'partially_paid_amount' => 0.00,
                'maintenance_costs' => 0.00,
                'net_position' => 0.00,
                'collection_rate' => 0.00,

                'paid_invoices_count' => 0,
                'partially_paid_count' => 0,
                'overdue_count' => 0,
            ];
        }

        // Expected Rent (Invoiced within date range)
        $invoicedRows = (clone $rentInvoicesQuery)
            ->selectRaw('currency, SUM(amount) as total')
            ->where('status', '!=', RentInvoice::STATUS_CANCELLED)
            ->whereBetween('due_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereIn('currency', $supportedCurrencies)
            ->groupBy('currency')
            ->get();

        foreach ($invoicedRows as $row) {
            $curr = strtoupper((string) $row->currency);
            if (isset($financialByCurrency[$curr])) {
                $financialByCurrency[$curr]['expected_rent'] = round((float) $row->total, 2);
            }
        }

        // Collected Rent (Payments received within date range)
        $collectedRows = (clone $paymentsQuery)
            ->selectRaw('currency, SUM(amount) as total')
            ->where('status', Payment::STATUS_COMPLETED)
            ->whereBetween('payment_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereIn('currency', $supportedCurrencies)
            ->groupBy('currency')
            ->get();

        foreach ($collectedRows as $row) {
            $curr = strtoupper((string) $row->currency);
            if (isset($financialByCurrency[$curr])) {
                $financialByCurrency[$curr]['collected_rent'] = round((float) $row->total, 2);
            }
        }

        // Maintenance Expenses within date range
        $expenseRows = (clone $maintenanceExpensesQuery)
            ->selectRaw('currency, SUM(amount) as total')
            ->whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereIn('currency', $supportedCurrencies)
            ->groupBy('currency')
            ->get();

        foreach ($expenseRows as $row) {
            $curr = strtoupper((string) $row->currency);
            if (isset($financialByCurrency[$curr])) {
                $financialByCurrency[$curr]['maintenance_costs'] = round((float) $row->total, 2);
            }
        }

        // Partially Paid Invoices Breakdown
        $partiallyPaidRows = (clone $rentInvoicesQuery)
            ->selectRaw('currency, COUNT(*) as cnt, SUM(amount - amount_paid) as balance_due')
            ->where('status', RentInvoice::STATUS_PARTIALLY_PAID)
            ->whereIn('currency', $supportedCurrencies)
            ->groupBy('currency')
            ->get();

        foreach ($partiallyPaidRows as $row) {
            $curr = strtoupper((string) $row->currency);
            if (isset($financialByCurrency[$curr])) {
                $financialByCurrency[$curr]['partially_paid_count'] = (int) $row->cnt;
                $financialByCurrency[$curr]['partially_paid_amount'] = round((float) $row->balance_due, 2);
            }
        }

        // Overdue Invoices Breakdown
        $overdueRows = (clone $rentInvoicesQuery)
            ->selectRaw('currency, COUNT(*) as cnt, SUM(amount - amount_paid) as balance_due')
            ->where('status', '!=', RentInvoice::STATUS_CANCELLED)
            ->where('status', '!=', RentInvoice::STATUS_PAID)
            ->whereDate('due_date', '<', now()->toDateString())
            ->whereIn('currency', $supportedCurrencies)
            ->groupBy('currency')
            ->get();

        foreach ($overdueRows as $row) {
            $curr = strtoupper((string) $row->currency);
            if (isset($financialByCurrency[$curr])) {
                $financialByCurrency[$curr]['overdue_count'] = (int) $row->cnt;
                $financialByCurrency[$curr]['overdue_rent'] = round((float) $row->balance_due, 2);
            }
        }

        // Calculate Derived Metrics
        foreach ($supportedCurrencies as $curr) {
            $expected = $financialByCurrency[$curr]['expected_rent'];
            $collected = $financialByCurrency[$curr]['collected_rent'];
            $costs = $financialByCurrency[$curr]['maintenance_costs'];

            $financialByCurrency[$curr]['outstanding_rent'] = round(max(0, $expected - $collected), 2);
            $financialByCurrency[$curr]['collection_rate'] = $expected > 0 ? round(($collected / $expected) * 100, 1) : 0;
            $financialByCurrency[$curr]['net_position'] = round($collected - $costs, 2);
        }

        /*
        |--------------------------------------------------------------------------
        | Revenue Share By Property (Percentage breakdown)
        |--------------------------------------------------------------------------
        */

        $propertiesList = (clone $propertiesQuery)->with('coverPhoto')->get();
        $propertyRevenueShare = [];

        foreach ($supportedCurrencies as $currency) {
            $propertyRevenueShare[$currency] = [
                'labels' => [],
                'values' => [],
            ];

            $propertyTotals = [];
            foreach ($propertiesList as $propertyItem) {
                $propInvoiced = RentInvoice::query()
                    ->whereHas('lease.unit', fn ($q) => $q->where('property_id', $propertyItem->id))
                    ->where('status', '!=', RentInvoice::STATUS_CANCELLED)
                    ->where('currency', $currency)
                    ->whereBetween('due_date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->sum('amount');

                if ($propInvoiced > 0) {
                    $propertyTotals[$propertyItem->name] = (float) $propInvoiced;
                }
            }

            $totalSum = array_sum($propertyTotals);
            if ($totalSum > 0) {
                foreach ($propertyTotals as $pName => $pAmount) {
                    $pct = round(($pAmount / $totalSum) * 100, 1);
                    $propertyRevenueShare[$currency]['labels'][] = "{$pName} ({$pct}%)";
                    $propertyRevenueShare[$currency]['values'][] = round($pAmount, 2);
                }
            } else {
                $propertyRevenueShare[$currency]['labels'] = ['No Revenue Data'];
                $propertyRevenueShare[$currency]['values'] = [0];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 12-Month Revenue Trend (Invoiced vs Collected)
        |--------------------------------------------------------------------------
        */

        $pastMonths = [];
        $pastMonthLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $mDate = $now->copy()->subMonths($i);
            $mKey = $mDate->format('Y-m');
            $pastMonths[$mKey] = [
                'start' => $mDate->copy()->startOfMonth(),
                'end' => $mDate->copy()->endOfMonth(),
                'label' => $mDate->format('M Y'),
            ];
            $pastMonthLabels[] = $mDate->format('M Y');
        }

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
        | Recent Transactions & Maintenance Requests
        |--------------------------------------------------------------------------
        */

        $recentPayments = (clone $paymentsQuery)
            ->with(['rentInvoice.lease.tenant', 'rentInvoice.lease.unit.property', 'recordedBy'])
            ->latest('created_at')
            ->limit(6)
            ->get();

        $recentMaintenanceRequests = (clone $maintenanceRequestsQuery)
            ->with(['unit.property', 'tenant', 'assignedTo'])
            ->latest('created_at')
            ->limit(6)
            ->get();

        return view('owner.dashboard', [
            'owner' => $owner,
            'ownersList' => $ownersList,
            'timeframe' => $timeframe,
            'startDate' => $startDate,
            'endDate' => $endDate,

            'totalProperties' => $totalProperties,
            'totalUnits' => $totalUnits,
            'occupiedUnits' => $occupiedUnits,
            'vacantUnits' => $vacantUnits,
            'occupancyRate' => $occupancyRate,
            'activeTenants' => $activeTenants,

            'financialByCurrency' => $financialByCurrency,
            'supportedCurrencies' => $supportedCurrencies,
            'propertyRevenueShare' => $propertyRevenueShare,
            'monthlyRevenueTrend' => $monthlyRevenueTrend,

            'recentPayments' => $recentPayments,
            'recentMaintenanceRequests' => $recentMaintenanceRequests,
            'propertiesList' => $propertiesList,
        ]);
    }
}
