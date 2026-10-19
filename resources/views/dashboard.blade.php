@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-heading', 'Dashboard')

@php
    use App\Support\Currency;

    /*
    |--------------------------------------------------------------------------
    | Dashboard Values
    |--------------------------------------------------------------------------
    */

    $totalProperties = (int) ($totalProperties ?? 0);
    $totalUnits = (int) ($totalUnits ?? 0);
    $occupiedUnits = (int) ($occupiedUnits ?? 0);
    $vacantUnits = (int) ($vacantUnits ?? 0);
    $activeTenants = (int) ($activeTenants ?? 0);
    $openMaintenanceRequests = (int) ($openMaintenanceRequests ?? 0);
    $overdueInvoicesCount = (int) ($overdueInvoicesCount ?? 0);

    $occupancyRate = (float) ($occupancyRate ?? 0);

    $expiringLeases = $expiringLeases ?? collect();
    $recentPayments = $recentPayments ?? collect();
    $recentMaintenanceRequests = $recentMaintenanceRequests ?? collect();

    /*
    |--------------------------------------------------------------------------
    | Multi-Currency Financial Data
    |--------------------------------------------------------------------------
    |
    | This matches DashboardController.
    |
    | $financialByCurrency = [
    |     'USD' => [
    |         'rent_invoiced' => 0,
    |         'rent_collected' => 0,
    |         'outstanding_rent' => 0,
    |         'maintenance_expenses' => 0,
    |         'net_position' => 0,
    |         'collection_rate' => 0,
    |         ...
    |     ],
    |     'KES' => [...],
    |     'CDF' => [...],
    | ];
    |
    */

    $financialByCurrency = $financialByCurrency ?? [];

    $safeOccupancyRate = min(
        100,
        max(0, $occupancyRate)
    );

    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | Role Permissions
    |--------------------------------------------------------------------------
    */

    $canManageProperties = $user->hasRole([
        'administrator',
        'property_manager',
        'reception_staff',
    ]);

    $canManagePropertyTypes = $user->hasRole([
        'administrator',
        'property_manager',
    ]);

    $canManageFinance = $user->hasRole([
        'administrator',
        'accountant',
    ]);

    $canManageMaintenance = $user->hasRole([
        'administrator',
        'property_manager',
        'maintenance_staff',
    ]);

    $canViewReports = $user->hasRole([
        'administrator',
        'property_manager',
        'accountant',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Currency Formatter
    |--------------------------------------------------------------------------
    |
    | Invalid currencies are never silently converted to USD.
    |
    */

    $formatCurrency = function (
        $amount,
        $currency
    ): string {
        $currency = strtoupper(
            trim((string) $currency)
        );

        if (!in_array(
            $currency,
            Currency::SUPPORTED,
            true
        )) {
            return number_format(
                (float) $amount,
                2
            );
        }

        return Currency::format(
            (float) $amount,
            $currency
        );
    };

    /*
    |--------------------------------------------------------------------------
    | Currency Data Helper
    |--------------------------------------------------------------------------
    */

    $getCurrencyFinancial = function (
        string $currency
    ) use ($financialByCurrency): array {
        return $financialByCurrency[$currency] ?? [
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
    };
@endphp

@push('styles')
<style>
    .dashboard-page {
        --dashboard-navy: var(--pm-navy, #16233a);
        --dashboard-teal: var(--pm-teal, #0d9488);
        --dashboard-teal-dark: var(--pm-teal-dark, #0f766e);
        --dashboard-muted: var(--pm-muted, #64748b);
        --dashboard-border: var(--pm-border, #e2e8f0);
        --dashboard-green: var(--pm-green, #16a34a);
        --dashboard-amber: var(--pm-amber, #d97706);
        --dashboard-red: var(--pm-red, #dc2626);
        --dashboard-blue: var(--pm-blue, #2563eb);

        padding-bottom: 2rem;
    }

    .analytics-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.65fr) minmax(300px, 0.95fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 991px) {
        .analytics-grid {
            grid-template-columns: 1fr;
        }
    }

    .currency-pills {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: #f1f5f9;
        padding: 0.2rem;
        border-radius: 0.6rem;
    }

    .currency-pill {
        border: none;
        background: transparent;
        color: var(--dashboard-muted);
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.25rem 0.65rem;
        border-radius: 0.45rem;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .currency-pill.active {
        background: #fff;
        color: var(--dashboard-teal-dark);
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
    }

    .financial-quick-stats {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 1200px) {
        .financial-quick-stats {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .financial-quick-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 480px) {
        .financial-quick-stats {
            grid-template-columns: 1fr;
        }
    }

    .fin-stat-card {
        position: relative;
        display: block;
        padding: 1.1rem;
        background: #fff;
        border: 1px solid var(--dashboard-border);
        border-radius: 0.9rem;
        text-decoration: none;
        color: inherit;
        transition: all 0.15s ease;
    }

    .fin-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        border-color: rgba(13, 148, 136, 0.4);
        color: inherit;
    }

    .fin-stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }

    .fin-stat-icon {
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.65rem;
        font-size: 1.1rem;
    }

    .fin-stat-icon.teal { background: rgba(13, 148, 136, 0.1); color: var(--dashboard-teal); }
    .fin-stat-icon.green { background: rgba(22, 163, 74, 0.1); color: var(--dashboard-green); }
    .fin-stat-icon.amber { background: rgba(217, 119, 6, 0.1); color: var(--dashboard-amber); }
    .fin-stat-icon.blue { background: rgba(37, 99, 235, 0.1); color: var(--dashboard-blue); }
    .fin-stat-icon.red { background: rgba(220, 38, 38, 0.1); color: var(--dashboard-red); }

    .fin-stat-label {
        color: var(--dashboard-muted);
        font-size: 0.78rem;
        font-weight: 600;
    }

    .fin-stat-value {
        color: var(--dashboard-navy);
        font-size: 1.45rem;
        font-weight: 700;
        line-height: 1.25;
        letter-spacing: -0.02em;
    }

    .fin-stat-meta {
        margin-top: 0.4rem;
        color: var(--dashboard-muted);
        font-size: 0.72rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    .dashboard-welcome {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .dashboard-welcome h1 {
        margin: 0;
        color: var(--dashboard-navy);
        font-size: 1.7rem;
        font-weight: 700;
        letter-spacing: -.025em;
    }

    .dashboard-welcome p {
        margin: .35rem 0 0;
        color: var(--dashboard-muted);
        font-size: .92rem;
    }

    .dashboard-welcome-actions {
        display: flex;
        align-items: center;
        gap: .75rem;
        flex-wrap: wrap;
    }

    .dashboard-primary-button,
    .dashboard-outline-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        min-height: 42px;
        padding: .65rem 1rem;
        border-radius: .65rem;
        text-decoration: none;
        font-size: .875rem;
        font-weight: 600;
        transition: .15s ease;
    }

    .dashboard-primary-button {
        color: #fff;
        background: var(--dashboard-teal);
        border: 1px solid var(--dashboard-teal);
    }

    .dashboard-primary-button:hover {
        color: #fff;
        background: var(--dashboard-teal-dark);
        border-color: var(--dashboard-teal-dark);
        transform: translateY(-1px);
    }

    .dashboard-outline-button {
        color: var(--dashboard-navy);
        background: #fff;
        border: 1px solid var(--dashboard-border);
    }

    .dashboard-outline-button:hover {
        color: var(--dashboard-teal-dark);
        border-color: var(--dashboard-teal);
        background: #f8fafc;
    }

    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .hero-stat {
        position: relative;
        display: block;
        overflow: hidden;
        padding: 1.15rem;
        color: inherit;
        background: #fff;
        border: 1px solid var(--dashboard-border);
        border-radius: .9rem;
        text-decoration: none;
        transition: .15s ease;
    }

    .hero-stat:hover {
        color: inherit;
        transform: translateY(-2px);
        border-color: rgba(13, 148, 136, .35);
        box-shadow: 0 10px 25px rgba(15, 23, 42, .07);
    }

    .hero-stat::after {
        content: "";
        position: absolute;
        right: -35px;
        bottom: -45px;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: rgba(13, 148, 136, .06);
    }

    .hero-stat-content {
        position: relative;
        z-index: 1;
    }

    .hero-stat-icon {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: .7rem;
        background: rgba(13, 148, 136, .1);
        color: var(--dashboard-teal);
        font-size: 1.1rem;
    }

    .hero-stat-label {
        margin-top: .85rem;
        color: var(--dashboard-muted);
        font-size: .8rem;
        font-weight: 600;
    }

    .hero-stat-value {
        margin-top: .15rem;
        color: var(--dashboard-navy);
        font-size: 1.6rem;
        line-height: 1.2;
        font-weight: 700;
        letter-spacing: -.03em;
    }

    .hero-stat-meta {
        display: flex;
        align-items: center;
        gap: .35rem;
        margin-top: .4rem;
        color: var(--dashboard-muted);
        font-size: .75rem;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.65fr) minmax(300px, .9fr);
        gap: 1rem;
        align-items: start;
    }

    .dashboard-column {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .dashboard-card {
        overflow: hidden;
        background: #fff;
        border: 1px solid var(--dashboard-border);
        border-radius: .9rem;
    }

    .dashboard-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.15rem;
        border-bottom: 1px solid var(--dashboard-border);
    }

    .dashboard-card-title {
        margin: 0;
        color: var(--dashboard-navy);
        font-size: .95rem;
        font-weight: 700;
    }

    .dashboard-card-subtitle {
        margin: .2rem 0 0;
        color: var(--dashboard-muted);
        font-size: .75rem;
    }

    .dashboard-card-link {
        color: var(--dashboard-teal-dark);
        font-size: .78rem;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
    }

    .dashboard-card-link:hover {
        text-decoration: underline;
    }

    .dashboard-card-body {
        padding: 1.15rem;
    }

    .finance-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .85rem;
    }

    .finance-item {
        padding: .95rem;
        background: #f8fafc;
        border: 1px solid #edf1f5;
        border-radius: .7rem;
    }

    .finance-item-label {
        color: var(--dashboard-muted);
        font-size: .75rem;
        font-weight: 600;
    }

    .finance-item-value {
        margin-top: .3rem;
        color: var(--dashboard-navy);
        font-size: 1.05rem;
        font-weight: 700;
    }

    .finance-item-value.positive {
        color: var(--dashboard-green);
    }

    .finance-item-value.warning {
        color: var(--dashboard-amber);
    }

    .finance-item-value.danger {
        color: var(--dashboard-red);
    }

    .currency-section {
        margin-bottom: 1.25rem;
    }

    .currency-section:last-child {
        margin-bottom: 0;
    }

    .currency-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: .7rem;
    }

    .currency-section-title {
        display: flex;
        align-items: center;
        gap: .5rem;
        color: var(--dashboard-navy);
        font-size: .82rem;
        font-weight: 700;
    }

    .currency-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 42px;
        padding: .25rem .45rem;
        border-radius: .4rem;
        background: rgba(13, 148, 136, .1);
        color: var(--dashboard-teal-dark);
        font-size: .68rem;
        font-weight: 800;
    }

    .currency-trend {
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        font-size: .7rem;
        font-weight: 700;
    }

    .currency-trend.positive {
        color: var(--dashboard-green);
    }

    .currency-trend.negative {
        color: var(--dashboard-red);
    }

    .currency-trend.neutral {
        color: var(--dashboard-muted);
    }

    .progress-section {
        margin-top: 1.1rem;
    }

    .progress-row {
        margin-bottom: .95rem;
    }

    .progress-row:last-child {
        margin-bottom: 0;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: .4rem;
        color: var(--dashboard-muted);
        font-size: .75rem;
        font-weight: 600;
    }

    .progress-track {
        width: 100%;
        height: 8px;
        overflow: hidden;
        border-radius: 999px;
        background: #e9eef3;
    }

    .progress-bar-custom {
        height: 100%;
        border-radius: inherit;
        background: var(--dashboard-teal);
    }

    .progress-bar-collection {
        background: var(--dashboard-blue);
    }

    .occupancy-summary {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .occupancy-circle {
        position: relative;
        flex: 0 0 auto;
        width: 90px;
        height: 90px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background:
            conic-gradient(
                var(--dashboard-teal) calc(var(--occupancy) * 1%),
                #e9eef3 0
            );
    }

    .occupancy-circle::before {
        content: "";
        position: absolute;
        inset: 9px;
        border-radius: 50%;
        background: #fff;
    }

    .occupancy-circle-value {
        position: relative;
        z-index: 1;
        color: var(--dashboard-navy);
        font-size: 1.15rem;
        font-weight: 700;
    }

    .occupancy-title {
        color: var(--dashboard-navy);
        font-size: .88rem;
        font-weight: 700;
    }

    .occupancy-description {
        margin-top: .25rem;
        color: var(--dashboard-muted);
        font-size: .75rem;
        line-height: 1.5;
    }

    .occupancy-breakdown {
        display: flex;
        gap: 1rem;
        margin-top: .65rem;
    }

    .occupancy-breakdown-item {
        color: var(--dashboard-muted);
        font-size: .72rem;
    }

    .occupancy-breakdown-item strong {
        color: var(--dashboard-navy);
    }

    .quick-actions {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .75rem;
    }

    .quick-action {
        display: flex;
        align-items: center;
        gap: .7rem;
        min-height: 70px;
        padding: .8rem;
        color: var(--dashboard-navy);
        background: #fff;
        border: 1px solid var(--dashboard-border);
        border-radius: .7rem;
        text-decoration: none;
        transition: .15s ease;
    }

    .quick-action:hover {
        color: var(--dashboard-teal-dark);
        border-color: rgba(13, 148, 136, .35);
        background: #f8fffe;
        transform: translateY(-1px);
    }

    .quick-action-icon {
        flex: 0 0 auto;
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: .6rem;
        background: rgba(13, 148, 136, .1);
        color: var(--dashboard-teal);
    }

    .quick-action-title {
        font-size: .78rem;
        font-weight: 700;
    }

    .quick-action-description {
        margin-top: .15rem;
        color: var(--dashboard-muted);
        font-size: .68rem;
    }

    .attention-list {
        display: flex;
        flex-direction: column;
    }

    .attention-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: .9rem 1.15rem;
        color: inherit;
        text-decoration: none;
        border-bottom: 1px solid #eef2f6;
        transition: background-color .15s ease;
    }

    .attention-link:hover {
        color: inherit;
        background: #f8fafc;
    }

    .attention-left {
        display: flex;
        align-items: center;
        gap: .75rem;
        min-width: 0;
    }

    .attention-icon {
        flex: 0 0 auto;
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: .6rem;
        background: rgba(217, 119, 6, .1);
        color: var(--dashboard-amber);
    }

    .attention-icon.danger {
        background: rgba(220, 38, 38, .1);
        color: var(--dashboard-red);
    }

    .attention-icon.blue {
        background: rgba(37, 99, 235, .1);
        color: var(--dashboard-blue);
    }

    .attention-title {
        color: var(--dashboard-navy);
        font-size: .82rem;
        font-weight: 700;
    }

    .attention-description {
        margin-top: .15rem;
        color: var(--dashboard-muted);
        font-size: .72rem;
        line-height: 1.5;
    }

    .attention-arrow {
        color: #94a3b8;
    }

    .dashboard-list {
        display: flex;
        flex-direction: column;
    }

    .dashboard-list-item {
        display: flex;
        align-items: center;
        gap: .8rem;
        padding: .85rem 1.15rem;
        border-bottom: 1px solid #eef2f6;
    }

    .dashboard-list-item:last-child {
        border-bottom: 0;
    }

    .dashboard-list-icon {
        flex: 0 0 auto;
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: .6rem;
        background: #f1f5f9;
        color: var(--dashboard-navy);
    }

    .dashboard-list-content {
        min-width: 0;
        flex: 1;
    }

    .dashboard-list-title {
        overflow: hidden;
        color: var(--dashboard-navy);
        font-size: .78rem;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .dashboard-list-meta {
        margin-top: .15rem;
        color: var(--dashboard-muted);
        font-size: .68rem;
    }

    .dashboard-list-value {
        flex: 0 0 auto;
        color: var(--dashboard-navy);
        font-size: .75rem;
        font-weight: 700;
        text-align: right;
    }

    .dashboard-badge {
        display: inline-flex;
        align-items: center;
        padding: .25rem .5rem;
        border-radius: 999px;
        font-size: .65rem;
        font-weight: 700;
    }

    .dashboard-badge-warning {
        color: #92400e;
        background: #fef3c7;
    }

    .dashboard-badge-danger {
        color: #991b1b;
        background: #fee2e2;
    }

    .dashboard-badge-success {
        color: #166534;
        background: #dcfce7;
    }

    .dashboard-badge-info {
        color: #1e40af;
        background: #dbeafe;
    }

    .dashboard-empty {
        padding: 1.5rem;
        text-align: center;
        color: var(--dashboard-muted);
    }

    .dashboard-empty-icon {
        margin-bottom: .5rem;
        color: #94a3b8;
        font-size: 1.5rem;
    }

    .dashboard-empty-text {
        font-size: .8rem;
    }

    .module-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: .75rem;
        padding: 1.15rem;
    }

    .module-card {
        display: flex;
        align-items: flex-start;
        gap: .7rem;
        padding: .85rem;
        color: var(--dashboard-navy);
        border: 1px solid var(--dashboard-border);
        border-radius: .7rem;
        text-decoration: none;
        transition: .15s ease;
    }

    .module-card:hover {
        color: var(--dashboard-teal-dark);
        border-color: rgba(13, 148, 136, .35);
        background: #f8fffe;
    }

    .module-icon {
        flex: 0 0 auto;
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: .55rem;
        background: #f1f5f9;
        color: var(--dashboard-navy);
        font-size: .9rem;
    }

    .module-card:hover .module-icon {
        background: rgba(13, 148, 136, .1);
        color: var(--dashboard-teal);
    }

    .module-title {
        font-size: .77rem;
        font-weight: 700;
    }

    .module-description {
        margin-top: .15rem;
        color: var(--dashboard-muted);
        font-size: .67rem;
    }

    html[data-theme="dark"] .dashboard-page {
        --dashboard-surface: #16243A;
        --dashboard-surface-raised: #1B2A42;
        --dashboard-subtle: #0F1A2B;
        --dashboard-border: #2C3D58;
        --dashboard-muted: #AAB8CC;
    }

    html[data-theme="dark"] .dashboard-outline-button,
    html[data-theme="dark"] .hero-stat,
    html[data-theme="dark"] .dashboard-card,
    html[data-theme="dark"] .quick-action {
        background: var(--dashboard-surface);
        border-color: var(--dashboard-border);
        box-shadow: 0 10px 24px rgba(0, 0, 0, .14);
    }

    html[data-theme="dark"] .hero-stat:hover,
    html[data-theme="dark"] .quick-action:hover,
    html[data-theme="dark"] .module-card:hover,
    html[data-theme="dark"] .dashboard-outline-button:hover {
        background: var(--dashboard-surface-raised);
        border-color: rgba(94, 234, 212, .48);
    }

    html[data-theme="dark"] .finance-item,
    html[data-theme="dark"] .dashboard-list-icon,
    html[data-theme="dark"] .module-icon {
        background: var(--dashboard-subtle);
        border-color: var(--dashboard-border);
    }

    html[data-theme="dark"] .currency-badge {
        background: rgba(45, 212, 191, .12);
    }

    html[data-theme="dark"] .progress-track {
        background: #0C1524;
    }

    html[data-theme="dark"] .occupancy-circle {
        background: conic-gradient(
            var(--dashboard-teal) calc(var(--occupancy) * 1%),
            #0C1524 0
        );
    }

    html[data-theme="dark"] .occupancy-circle::before {
        background: var(--dashboard-surface);
    }

    html[data-theme="dark"] .attention-link,
    html[data-theme="dark"] .dashboard-list-item {
        border-bottom-color: var(--dashboard-border);
    }

    html[data-theme="dark"] .attention-link:hover {
        background: rgba(94, 234, 212, .06);
    }

    html[data-theme="dark"] .hero-stat::after {
        background: rgba(45, 212, 191, .10);
    }

    html[data-theme="dark"] .dashboard-badge-warning {
        color: #FDE68A;
        background: rgba(245, 158, 11, .18);
    }

    html[data-theme="dark"] .dashboard-badge-danger {
        color: #FECDD3;
        background: rgba(244, 63, 94, .18);
    }

    html[data-theme="dark"] .dashboard-badge-success {
        color: #BBF7D0;
        background: rgba(34, 197, 94, .18);
    }

    html[data-theme="dark"] .dashboard-badge-info {
        color: #BFDBFE;
        background: rgba(59, 130, 246, .18);
    }

    @media (max-width: 1199.98px) {
        .dashboard-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767.98px) {
        .dashboard-welcome {
            align-items: flex-start;
            flex-direction: column;
        }

        .dashboard-welcome-actions {
            width: 100%;
        }

        .dashboard-welcome-actions a {
            flex: 1;
        }

        .dashboard-stats {
            grid-template-columns: 1fr;
        }

        .finance-grid,
        .quick-actions {
            grid-template-columns: 1fr;
        }

        .module-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 480px) {
        .module-grid {
            grid-template-columns: 1fr;
        }

        .occupancy-summary {
            align-items: flex-start;
            flex-direction: column;
        }

        .occupancy-breakdown {
            flex-wrap: wrap;
        }
    }
</style>
@endpush

@section('content')

<div class="dashboard-page">

    {{-- ================================================================
         WELCOME HEADER
    ================================================================= --}}

    <div class="dashboard-welcome">

        <div>

            <h1>
                Welcome back, {{ $user->name }}
            </h1>

            <p>
                Here's an overview of your property management operations.
            </p>

        </div>

        <div class="dashboard-welcome-actions">

            @if($canManageProperties)

                <a
                    href="{{ route('properties.create') }}"
                    class="dashboard-primary-button"
                >
                    <i class="bi bi-plus-lg"></i>
                    Add Property
                </a>

            @endif

            @if($canManageFinance)

                <a
                    href="{{ route('payments.create') }}"
                    class="dashboard-outline-button"
                >
                    <i class="bi bi-cash-coin"></i>
                    Record Payment
                </a>

            @endif

        </div>

    </div>

    {{-- Dashboard Filter Bar --}}
    @if(($ownersList && $ownersList->count() > 0) || ($filterPropertiesList && $filterPropertiesList->count() > 0))
        <div class="card border mb-4 shadow-sm" style="border-radius: 0.85rem; background: var(--dashboard-surface, #fff);">
            <div class="card-body p-3">
                <form action="{{ route('dashboard') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-auto">
                        <span class="fw-bold small text-muted">
                            <i class="bi bi-funnel me-1"></i> Dashboard Filter:
                        </span>
                    </div>

                    @if($user->isAdmin() && $ownersList && $ownersList->count() > 0)
                        <div class="col-md-3">
                            <select name="owner_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">-- All Owners --</option>
                                @foreach($ownersList as $ownerItem)
                                    <option value="{{ $ownerItem->id }}" {{ $selectedOwnerId == $ownerItem->id ? 'selected' : '' }}>
                                        {{ $ownerItem->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if($filterPropertiesList && $filterPropertiesList->count() > 0)
                        <div class="col-md-3">
                            <select name="property_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">-- All Properties --</option>
                                @foreach($filterPropertiesList as $propItem)
                                    <option value="{{ $propItem->id }}" {{ $selectedPropertyId == $propItem->id ? 'selected' : '' }}>
                                        {{ $propItem->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if($selectedOwnerId || $selectedPropertyId)
                        <div class="col-auto">
                            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i> Clear Filter
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    @endif

    {{-- ================================================================
         FINANCIAL & OPERATIONAL QUICK STATS
    ================================================================= --}}

    @if($canManageFinance)
        @php
            $primaryCurr = $supportedCurrencies[0] ?? 'USD';
            $primaryFin = $getCurrencyFinancial($primaryCurr);
            $collectedTodayVal = $primaryFin['collected_today'] ?? 0;
            $collectedThisMonthVal = $primaryFin['collected_this_month'] ?? 0;
            $outstandingThisMonthVal = $primaryFin['outstanding_this_month'] ?? 0;
            $partiallyPaidAmtVal = $primaryFin['partially_paid_amount'] ?? 0;
            $overdueAmtVal = $primaryFin['overdue_amount'] ?? 0;
        @endphp

        <div class="financial-quick-stats">

            {{-- 1. Rent Collected Today --}}
            <a href="{{ route('payments.index') }}" class="fin-stat-card">
                <div class="fin-stat-header">
                    <span class="fin-stat-label">Rent Collected Today</span>
                    <div class="fin-stat-icon green">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                </div>
                <div class="fin-stat-value">
                    {{ $formatCurrency($collectedTodayVal, $primaryCurr) }}
                </div>
                <div class="fin-stat-meta">
                    <i class="bi bi-clock-history me-1"></i> Today's payments
                </div>
            </a>

            {{-- 2. Rent Collected This Month --}}
            <a href="{{ route('payments.index') }}" class="fin-stat-card">
                <div class="fin-stat-header">
                    <span class="fin-stat-label">Collected This Month</span>
                    <div class="fin-stat-icon teal">
                        <i class="bi bi-wallet2"></i>
                    </div>
                </div>
                <div class="fin-stat-value">
                    {{ $formatCurrency($collectedThisMonthVal, $primaryCurr) }}
                </div>
                <div class="fin-stat-meta text-teal">
                    <i class="bi bi-graph-up me-1"></i> Current billing month
                </div>
            </a>

            {{-- 3. Outstanding For The Month --}}
            <a href="{{ route('rent-invoices.index', ['status' => 'unpaid']) }}" class="fin-stat-card">
                <div class="fin-stat-header">
                    <span class="fin-stat-label">Outstanding This Month</span>
                    <div class="fin-stat-icon amber">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
                <div class="fin-stat-value">
                    {{ $formatCurrency($outstandingThisMonthVal, $primaryCurr) }}
                </div>
                <div class="fin-stat-meta text-amber">
                    <i class="bi bi-exclamation-circle me-1"></i> Uncollected balance
                </div>
            </a>

            {{-- 4. Partially Paid Accounts --}}
            <a href="{{ route('rent-invoices.index', ['status' => 'partially_paid']) }}" class="fin-stat-card">
                <div class="fin-stat-header">
                    <span class="fin-stat-label">Partially Paid</span>
                    <div class="fin-stat-icon blue">
                        <i class="bi bi-pie-chart-fill"></i>
                    </div>
                </div>
                <div class="fin-stat-value">
                    {{ number_format($partiallyPaidTotalCount ?? 0) }} <span style="font-size: 0.8rem; font-weight: 500; color: var(--dashboard-muted);">accounts</span>
                </div>
                <div class="fin-stat-meta text-blue">
                    <i class="bi bi-cash me-1"></i> {{ $formatCurrency($partiallyPaidAmtVal, $primaryCurr) }} balance
                </div>
            </a>

            {{-- 5. Overdue Accounts / Invoices --}}
            <a href="{{ route('rent-invoices.index', ['status' => 'overdue']) }}" class="fin-stat-card">
                <div class="fin-stat-header">
                    <span class="fin-stat-label">Overdue Accounts</span>
                    <div class="fin-stat-icon red">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                </div>
                <div class="fin-stat-value text-danger">
                    {{ number_format($overdueInvoicesCount ?? 0) }} <span style="font-size: 0.8rem; font-weight: 500; color: var(--dashboard-muted);">overdue</span>
                </div>
                <div class="fin-stat-meta text-danger">
                    <i class="bi bi-shield-exclamation me-1"></i> {{ $formatCurrency($overdueAmtVal, $primaryCurr) }} past due
                </div>
            </a>

        </div>
    @endif


    {{-- ================================================================
         KPI STATISTICS
    ================================================================= --}}

    <div class="dashboard-stats">

        {{-- Properties --}}

        @if($canManageProperties)

            <a
                href="{{ route('properties.index') }}"
                class="hero-stat"
            >

        @else

            <div class="hero-stat">

        @endif

                <div class="hero-stat-content">

                    <div class="hero-stat-icon">
                        <i class="bi bi-buildings"></i>
                    </div>

                    <div class="hero-stat-label">
                        Properties
                    </div>

                    <div class="hero-stat-value">
                        {{ number_format($totalProperties) }}
                    </div>

                    <div class="hero-stat-meta">
                        <i class="bi bi-building"></i>
                        Managed properties
                    </div>

                </div>

        @if($canManageProperties)

            </a>

        @else

            </div>

        @endif


        {{-- Units --}}

        @if($canManageProperties)

            <a
                href="{{ route('units.index') }}"
                class="hero-stat"
            >

        @else

            <div class="hero-stat">

        @endif

                <div class="hero-stat-content">

                    <div class="hero-stat-icon">
                        <i class="bi bi-door-open"></i>
                    </div>

                    <div class="hero-stat-label">
                        Total Units
                    </div>

                    <div class="hero-stat-value">
                        {{ number_format($totalUnits) }}
                    </div>

                    <div class="hero-stat-meta">
                        <i class="bi bi-pie-chart"></i>
                        {{ number_format($occupancyRate, 1) }}% occupied
                    </div>

                </div>

        @if($canManageProperties)

            </a>

        @else

            </div>

        @endif


        {{-- Tenants --}}

        @if($canManageProperties)

            <a
                href="{{ route('tenants.index') }}"
                class="hero-stat"
            >

        @else

            <div class="hero-stat">

        @endif

                <div class="hero-stat-content">

                    <div class="hero-stat-icon">
                        <i class="bi bi-people"></i>
                    </div>

                    <div class="hero-stat-label">
                        Active Tenants
                    </div>

                    <div class="hero-stat-value">
                        {{ number_format($activeTenants) }}
                    </div>

                    <div class="hero-stat-meta">
                        <i class="bi bi-person-check"></i>
                        Current tenants
                    </div>

                </div>

        @if($canManageProperties)

            </a>

        @else

            </div>

        @endif


        {{-- Maintenance --}}

        @if($canManageMaintenance)

            <a
                href="{{ route('maintenance-requests.index') }}"
                class="hero-stat"
            >

        @else

            <div class="hero-stat">

        @endif

                <div class="hero-stat-content">

                    <div class="hero-stat-icon">
                        <i class="bi bi-tools"></i>
                    </div>

                    <div class="hero-stat-label">
                        Open Maintenance
                    </div>

                    <div class="hero-stat-value">
                        {{ number_format($openMaintenanceRequests) }}
                    </div>

                    <div class="hero-stat-meta">
                        <i class="bi bi-exclamation-circle"></i>
                        Requires attention
                    </div>

                </div>

        @if($canManageMaintenance)

            </a>

        @else

            </div>

        @endif

    </div>


    {{-- ================================================================
         INTERACTIVE VISUAL ANALYTICS & TREND GRAPHS
    ================================================================= --}}

    <div class="analytics-grid">

        {{-- 12-Month Revenue & Collection Trend Chart --}}
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div>
                    <h2 class="dashboard-card-title">
                        <i class="bi bi-graph-up-arrow me-1 text-teal"></i> 12-Month Revenue Collection & Invoicing Trend
                    </h2>
                    <p class="dashboard-card-subtitle">
                        Monthly rent invoiced vs completed collections
                    </p>
                </div>
                <div class="currency-pills">
                    @foreach($supportedCurrencies as $idx => $curr)
                        <button 
                            type="button" 
                            class="currency-pill {{ $idx === 0 ? 'active' : '' }}" 
                            data-currency-target="{{ $curr }}"
                            onclick="switchRevenueCurrency('{{ $curr }}', this)"
                        >
                            {{ $curr }}
                        </button>
                    @endforeach
                </div>
            </div>
            <div class="dashboard-card-body">
                <div class="chart-container" style="position: relative; height: 280px;">
                    <canvas id="dashboardRevenueChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Expense Distribution Breakdown --}}
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div>
                    <h2 class="dashboard-card-title">
                        <i class="bi bi-pie-chart-fill me-1 text-teal"></i> Expense Distribution
                    </h2>
                    <p class="dashboard-card-subtitle">
                        Maintenance expenses breakdown by type
                    </p>
                </div>
                <span class="currency-badge" id="expenseCurrencyBadge">{{ $supportedCurrencies[0] ?? 'USD' }}</span>
            </div>
            <div class="dashboard-card-body">
                <div class="chart-container" style="position: relative; height: 280px; display: flex; align-items: center; justify-content: center;">
                    <canvas id="dashboardExpenseChart"></canvas>
                </div>
            </div>
        </div>

    </div>

    {{-- Occupancy Trend (12 Months) Card --}}
    <div class="dashboard-card mb-4">
        <div class="dashboard-card-header">
            <div>
                <h2 class="dashboard-card-title">
                    <i class="bi bi-houses me-1 text-teal"></i> 12-Month Occupancy Rate & Unit Status Trend
                </h2>
                <p class="dashboard-card-subtitle">
                    Historical unit utilization rate over the last 12 months and current unit breakdown
                </p>
            </div>
            @if($canViewReports)
                <a href="{{ route('reports.index') }}" class="dashboard-card-link">
                    View Reports
                </a>
            @endif
        </div>
        <div class="dashboard-card-body">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div style="position: relative; height: 240px;">
                        <canvas id="dashboardOccupancyChart"></canvas>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div style="position: relative; height: 220px; display: flex; align-items: center; justify-content: center;">
                        <canvas id="dashboardUnitStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- ================================================================
         MAIN GRID
    ================================================================= --}}

    <div class="dashboard-grid">

        {{-- ============================================================
             LEFT COLUMN
        ============================================================= --}}

        <div class="dashboard-column">

            {{-- ========================================================
                 FINANCIAL OVERVIEW
            ========================================================= --}}

            @if($canManageFinance)

                <div class="dashboard-card">

                    <div class="dashboard-card-header">

                        <div>

                            <h2 class="dashboard-card-title">
                                Financial Overview
                            </h2>

                            <p class="dashboard-card-subtitle">
                                Financial position separated by currency
                            </p>

                        </div>

                        <a
                            href="{{ route('rent-invoices.index') }}"
                            class="dashboard-card-link"
                        >
                            View Invoices
                        </a>

                    </div>

                    <div class="dashboard-card-body">

                        @foreach(Currency::SUPPORTED as $currency)

                            @php

                                $financial =
                                    $getCurrencyFinancial($currency);

                                $invoiced = round(
                                    (float) (
                                        $financial['rent_invoiced']
                                        ?? 0
                                    ),
                                    2
                                );

                                $collected = round(
                                    (float) (
                                        $financial['rent_collected']
                                        ?? 0
                                    ),
                                    2
                                );

                                $outstanding = round(
                                    (float) (
                                        $financial['outstanding_rent']
                                        ?? 0
                                    ),
                                    2
                                );

                                $maintenance = round(
                                    (float) (
                                        $financial['maintenance_expenses']
                                        ?? 0
                                    ),
                                    2
                                );

                                $net = round(
                                    (float) (
                                        $financial['net_position']
                                        ?? 0
                                    ),
                                    2
                                );

                                $currencyCollection = (float) (
                                    $financial['collection_rate']
                                    ?? 0
                                );

                                $safeCurrencyCollection = min(
                                    100,
                                    max(0, $currencyCollection)
                                );

                                $collectionTrend =
                                    $financial['collection_rate_trend']
                                    ?? null;

                                $outstandingTrend =
                                    $financial['outstanding_rent_trend']
                                    ?? null;

                            @endphp

                            <div class="currency-section">

                                <div class="currency-section-header">

                                    <div class="currency-section-title">

                                        <span class="currency-badge">
                                            {{ $currency }}
                                        </span>

                                        {{ Currency::name($currency) }}

                                    </div>

                                    @if(!is_null($collectionTrend))

                                        <span
                                            class="
                                                currency-trend
                                                {{
                                                    $collectionTrend > 0
                                                        ? 'positive'
                                                        : (
                                                            $collectionTrend < 0
                                                                ? 'negative'
                                                                : 'neutral'
                                                        )
                                                }}
                                            "
                                        >

                                            @if($collectionTrend > 0)

                                                <i class="bi bi-arrow-up"></i>

                                            @elseif($collectionTrend < 0)

                                                <i class="bi bi-arrow-down"></i>

                                            @else

                                                <i class="bi bi-dash"></i>

                                            @endif

                                            {{ number_format(
                                                abs($collectionTrend),
                                                1
                                            ) }}
                                            pp vs previous period

                                        </span>

                                    @endif

                                </div>


                                <div class="finance-grid">

                                    {{-- Rent Invoiced --}}

                                    <div class="finance-item">

                                        <div class="finance-item-label">
                                            Rent Invoiced
                                        </div>

                                        <div class="finance-item-value">

                                            {{ $formatCurrency(
                                                $invoiced,
                                                $currency
                                            ) }}

                                        </div>

                                    </div>


                                    {{-- Rent Collected --}}

                                    <div class="finance-item">

                                        <div class="finance-item-label">
                                            Rent Collected
                                        </div>

                                        <div
                                            class="
                                                finance-item-value
                                                positive
                                            "
                                        >

                                            {{ $formatCurrency(
                                                $collected,
                                                $currency
                                            ) }}

                                        </div>

                                    </div>


                                    {{-- Outstanding --}}

                                    <div class="finance-item">

                                        <div class="finance-item-label">
                                            Outstanding Rent
                                        </div>

                                        <div
                                            class="
                                                finance-item-value
                                                {{
                                                    $outstanding > 0
                                                        ? 'warning'
                                                        : 'positive'
                                                }}
                                            "
                                        >

                                            {{ $formatCurrency(
                                                $outstanding,
                                                $currency
                                            ) }}

                                        </div>

                                    </div>


                                    {{-- Maintenance --}}

                                    <div class="finance-item">

                                        <div class="finance-item-label">
                                            Maintenance Expenses
                                        </div>

                                        <div
                                            class="
                                                finance-item-value
                                                danger
                                            "
                                        >

                                            {{ $formatCurrency(
                                                $maintenance,
                                                $currency
                                            ) }}

                                        </div>

                                    </div>


                                    {{-- Net Position --}}

                                    <div class="finance-item">

                                        <div class="finance-item-label">
                                            Net Position
                                        </div>

                                        <div
                                            class="
                                                finance-item-value
                                                {{
                                                    $net >= 0
                                                        ? 'positive'
                                                        : 'danger'
                                                }}
                                            "
                                        >

                                            {{ $formatCurrency(
                                                $net,
                                                $currency
                                            ) }}

                                        </div>

                                    </div>


                                    {{-- Collection Rate --}}

                                    <div class="finance-item">

                                        <div class="finance-item-label">
                                            Collection Rate
                                        </div>

                                        <div class="finance-item-value">

                                            {{ number_format(
                                                $currencyCollection,
                                                1
                                            ) }}%

                                        </div>

                                    </div>

                                </div>


                                {{-- Collection Progress --}}

                                <div class="progress-section">

                                    <div class="progress-row">

                                        <div class="progress-label">

                                            <span>
                                                {{ $currency }}
                                                Rent Collection
                                            </span>

                                            <span>
                                                {{ number_format(
                                                    $currencyCollection,
                                                    1
                                                ) }}%
                                            </span>

                                        </div>

                                        <div class="progress-track">

                                            <div
                                                class="
                                                    progress-bar-custom
                                                    progress-bar-collection
                                                "
                                                style="
                                                    width:
                                                    {{ $safeCurrencyCollection }}%;
                                                "
                                            ></div>

                                        </div>

                                    </div>


                                    @if(
                                        !is_null($outstandingTrend)
                                    )

                                        <div class="progress-label">

                                            <span>
                                                Outstanding trend
                                            </span>

                                            <span
                                                class="
                                                    currency-trend
                                                    {{
                                                        $outstandingTrend > 0
                                                            ? 'negative'
                                                            : (
                                                                $outstandingTrend < 0
                                                                    ? 'positive'
                                                                    : 'neutral'
                                                            )
                                                    }}
                                                "
                                            >

                                                @if($outstandingTrend > 0)

                                                    <i class="bi bi-arrow-up"></i>

                                                @elseif($outstandingTrend < 0)

                                                    <i class="bi bi-arrow-down"></i>

                                                @else

                                                    <i class="bi bi-dash"></i>

                                                @endif

                                                {{ number_format(
                                                    abs($outstandingTrend),
                                                    1
                                                ) }}%

                                            </span>

                                        </div>

                                    @endif

                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

            @endif


            {{-- ========================================================
                 OCCUPANCY
            ========================================================= --}}

            <div class="dashboard-card">

                <div class="dashboard-card-header">

                    <div>

                        <h2 class="dashboard-card-title">
                            Occupancy Overview
                        </h2>

                        <p class="dashboard-card-subtitle">
                            Current unit occupancy across your portfolio
                        </p>

                    </div>

                    @if($canManageProperties)

                        <a
                            href="{{ route('units.index') }}"
                            class="dashboard-card-link"
                        >
                            View Units
                        </a>

                    @endif

                </div>

                <div class="dashboard-card-body">

                    <div class="occupancy-summary">

                        <div
                            class="occupancy-circle"
                            style="
                                --occupancy:
                                {{ $safeOccupancyRate }};
                            "
                            aria-label="
                                Occupancy rate:
                                {{ $occupancyRate }}%
                            "
                        >

                            <span class="occupancy-circle-value">

                                {{ number_format(
                                    $occupancyRate,
                                    1
                                ) }}%

                            </span>

                        </div>

                        <div>

                            <div class="occupancy-title">

                                {{ number_format(
                                    $occupiedUnits
                                ) }}
                                occupied units

                            </div>

                            <div class="occupancy-description">

                                There are currently
                                {{ number_format($vacantUnits) }}
                                vacant units across the portfolio.

                            </div>

                            <div class="occupancy-breakdown">

                                <div class="occupancy-breakdown-item">

                                    Occupied:

                                    <strong>
                                        {{ number_format(
                                            $occupiedUnits
                                        ) }}
                                    </strong>

                                </div>

                                <div class="occupancy-breakdown-item">

                                    Vacant:

                                    <strong>
                                        {{ number_format(
                                            $vacantUnits
                                        ) }}
                                    </strong>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ========================================================
                 LEASE RENEWALS
            ========================================================= --}}

            @if($canManageProperties)

                <div class="dashboard-card">

                    <div class="dashboard-card-header">

                        <div>

                            <h2 class="dashboard-card-title">
                                Lease Renewals
                            </h2>

                            <p class="dashboard-card-subtitle">
                                Leases ending within the next 30 days
                            </p>

                        </div>

                        <a
                            href="{{ route('leases.index') }}"
                            class="dashboard-card-link"
                        >
                            View Leases
                        </a>

                    </div>

                    @if($expiringLeases->count() > 0)

                        <div class="dashboard-list">

                            @foreach($expiringLeases as $lease)

                                <a
                                    href="{{ route(
                                        'leases.show',
                                        $lease
                                    ) }}"
                                    class="
                                        dashboard-list-item
                                        text-decoration-none
                                    "
                                >

                                    <div class="dashboard-list-icon">
                                        <i class="bi bi-calendar-event"></i>
                                    </div>

                                    <div class="dashboard-list-content">

                                        <div class="dashboard-list-title">

                                            {{ $lease->tenant->name
                                                ?? 'Tenant' }}

                                        </div>

                                        <div class="dashboard-list-meta">

                                            @if($lease->unit)

                                                Unit
                                                {{
                                                    $lease
                                                        ->unit
                                                        ->unit_number
                                                    ?? $lease->unit->id
                                                }}

                                            @endif

                                            @if($lease->unit?->property)

                                                ·
                                                {{
                                                    $lease
                                                        ->unit
                                                        ->property
                                                        ->name
                                                }}

                                            @endif

                                        </div>

                                    </div>

                                    <div class="dashboard-list-value">

                                        @if($lease->end_date)

                                            {{
                                                \Carbon\Carbon::parse(
                                                    $lease->end_date
                                                )->format('d M Y')
                                            }}

                                        @endif

                                    </div>

                                </a>

                            @endforeach

                        </div>

                    @else

                        <div class="dashboard-empty">

                            <div class="dashboard-empty-icon">
                                <i class="bi bi-calendar-check"></i>
                            </div>

                            <div class="dashboard-empty-text">
                                No leases are expiring within the next
                                30 days.
                            </div>

                        </div>

                    @endif

                </div>

            @endif


            {{-- ========================================================
                 SYSTEM MODULES
            ========================================================= --}}

            <div class="dashboard-card">

                <div class="dashboard-card-header">

                    <div>

                        <h2 class="dashboard-card-title">
                            System Modules
                        </h2>

                        <p class="dashboard-card-subtitle">
                            Quickly access the areas you manage
                        </p>

                    </div>

                </div>

                <div class="module-grid">

                    @if($canManageProperties)

                        <a
                            href="{{ route('owners.index') }}"
                            class="module-card"
                        >

                            <div class="module-icon">
                                <i class="bi bi-person-vcard"></i>
                            </div>

                            <div>

                                <div class="module-title">
                                    Owners
                                </div>

                                <div class="module-description">
                                    Property owners
                                </div>

                            </div>

                        </a>


                        <a
                            href="{{ route('properties.index') }}"
                            class="module-card"
                        >

                            <div class="module-icon">
                                <i class="bi bi-buildings"></i>
                            </div>

                            <div>

                                <div class="module-title">
                                    Properties
                                </div>

                                <div class="module-description">
                                    Manage properties
                                </div>

                            </div>

                        </a>


                        <a
                            href="{{ route('units.index') }}"
                            class="module-card"
                        >

                            <div class="module-icon">
                                <i class="bi bi-door-open"></i>
                            </div>

                            <div>

                                <div class="module-title">
                                    Units
                                </div>

                                <div class="module-description">
                                    Rental units
                                </div>

                            </div>

                        </a>


                        <a
                            href="{{ route('tenants.index') }}"
                            class="module-card"
                        >

                            <div class="module-icon">
                                <i class="bi bi-people"></i>
                            </div>

                            <div>

                                <div class="module-title">
                                    Tenants
                                </div>

                                <div class="module-description">
                                    Manage tenants
                                </div>

                            </div>

                        </a>


                        <a
                            href="{{ route('leases.index') }}"
                            class="module-card"
                        >

                            <div class="module-icon">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>

                            <div>

                                <div class="module-title">
                                    Leases
                                </div>

                                <div class="module-description">
                                    Rental agreements
                                </div>

                            </div>

                        </a>

                    @endif


                    @if($canManagePropertyTypes)

                        <a
                            href="{{ route('property-types.index') }}"
                            class="module-card"
                        >

                            <div class="module-icon">
                                <i class="bi bi-tags"></i>
                            </div>

                            <div>

                                <div class="module-title">
                                    Property Types
                                </div>

                                <div class="module-description">
                                    Property categories
                                </div>

                            </div>

                        </a>

                    @endif


                    @if($canManageFinance)

                        <a
                            href="{{ route('rent-invoices.index') }}"
                            class="module-card"
                        >

                            <div class="module-icon">
                                <i class="bi bi-receipt"></i>
                            </div>

                            <div>

                                <div class="module-title">
                                    Rent Invoices
                                </div>

                                <div class="module-description">
                                    Rent billing
                                </div>

                            </div>

                        </a>


                        <a
                            href="{{ route('payments.index') }}"
                            class="module-card"
                        >

                            <div class="module-icon">
                                <i class="bi bi-cash-stack"></i>
                            </div>

                            <div>

                                <div class="module-title">
                                    Payments
                                </div>

                                <div class="module-description">
                                    Track payments
                                </div>

                            </div>

                        </a>

                    @endif


                    @if($canManageMaintenance)

                        <a
                            href="{{ route(
                                'maintenance-categories.index'
                            ) }}"
                            class="module-card"
                        >

                            <div class="module-icon">
                                <i class="bi bi-list-check"></i>
                            </div>

                            <div>

                                <div class="module-title">
                                    Maintenance Categories
                                </div>

                                <div class="module-description">
                                    Organize maintenance
                                </div>

                            </div>

                        </a>


                        <a
                            href="{{ route(
                                'maintenance-requests.index'
                            ) }}"
                            class="module-card"
                        >

                            <div class="module-icon">
                                <i class="bi bi-tools"></i>
                            </div>

                            <div>

                                <div class="module-title">
                                    Maintenance Requests
                                </div>

                                <div class="module-description">
                                    Track repairs
                                </div>

                            </div>

                        </a>


                        <a
                            href="{{ route(
                                'maintenance-expenses.index'
                            ) }}"
                            class="module-card"
                        >

                            <div class="module-icon">
                                <i class="bi bi-wallet2"></i>
                            </div>

                            <div>

                                <div class="module-title">
                                    Maintenance Expenses
                                </div>

                                <div class="module-description">
                                    Track expenses
                                </div>

                            </div>

                        </a>

                    @endif


                    @if($canViewReports)

                        <a
                            href="{{ route('reports.index') }}"
                            class="module-card"
                        >

                            <div class="module-icon">
                                <i class="bi bi-bar-chart-line"></i>
                            </div>

                            <div>

                                <div class="module-title">
                                    Reports
                                </div>

                                <div class="module-description">
                                    Business reports
                                </div>

                            </div>

                        </a>

                    @endif

                </div>

            </div>

        </div>


        {{-- ============================================================
             RIGHT COLUMN
        ============================================================= --}}

        <div class="dashboard-column">

            {{-- ========================================================
                 QUICK ACTIONS
            ========================================================= --}}

            <div class="dashboard-card">

                <div class="dashboard-card-header">

                    <div>

                        <h2 class="dashboard-card-title">
                            Quick Actions
                        </h2>

                        <p class="dashboard-card-subtitle">
                            Common tasks
                        </p>

                    </div>

                </div>

                <div class="dashboard-card-body">

                    <div class="quick-actions">

                        @if($canManageProperties)

                            <a
                                href="{{ route('properties.create') }}"
                                class="quick-action"
                            >

                                <div class="quick-action-icon">
                                    <i class="bi bi-building-add"></i>
                                </div>

                                <div>

                                    <div class="quick-action-title">
                                        Add Property
                                    </div>

                                    <div class="quick-action-description">
                                        Register a property
                                    </div>

                                </div>

                            </a>


                            <a
                                href="{{ route('tenants.create') }}"
                                class="quick-action"
                            >

                                <div class="quick-action-icon">
                                    <i class="bi bi-person-plus"></i>
                                </div>

                                <div>

                                    <div class="quick-action-title">
                                        Add Tenant
                                    </div>

                                    <div class="quick-action-description">
                                        Register a tenant
                                    </div>

                                </div>

                            </a>

                        @endif


                        @if($canManageFinance)

                            <a
                                href="{{ route('payments.create') }}"
                                class="quick-action"
                            >

                                <div class="quick-action-icon">
                                    <i class="bi bi-cash-coin"></i>
                                </div>

                                <div>

                                    <div class="quick-action-title">
                                        Record Payment
                                    </div>

                                    <div class="quick-action-description">
                                        Record rent payment
                                    </div>

                                </div>

                            </a>

                        @endif


                        @if($canManageMaintenance)

                            <a
                                href="{{ route(
                                    'maintenance-requests.create'
                                ) }}"
                                class="quick-action"
                            >

                                <div class="quick-action-icon">
                                    <i class="bi bi-tools"></i>
                                </div>

                                <div>

                                    <div class="quick-action-title">
                                        Maintenance Request
                                    </div>

                                    <div class="quick-action-description">
                                        Create a request
                                    </div>

                                </div>

                            </a>

                        @endif

                    </div>

                </div>

            </div>


            {{-- ========================================================
                 ATTENTION REQUIRED
            ========================================================= --}}

            <div class="dashboard-card">

                <div class="dashboard-card-header">

                    <div>

                        <h2 class="dashboard-card-title">
                            Attention Required
                        </h2>

                        <p class="dashboard-card-subtitle">
                            Items that may need action
                        </p>

                    </div>

                </div>

                <div class="attention-list">

                    @php

                        $attentionCurrencies = [];

                        foreach (Currency::SUPPORTED as $currency) {

                            $financial =
                                $getCurrencyFinancial($currency);

                            $amount = (float) (
                                $financial['outstanding_rent']
                                ?? 0
                            );

                            if ($amount > 0) {
                                $attentionCurrencies[$currency] =
                                    round($amount, 2);
                            }
                        }

                        $hasAttentionItems =
                            (
                                $canManageFinance &&
                                count($attentionCurrencies) > 0
                            )
                            ||
                            (
                                $canManageFinance &&
                                $overdueInvoicesCount > 0
                            )
                            ||
                            (
                                $canManageMaintenance &&
                                $openMaintenanceRequests > 0
                            )
                            ||
                            (
                                $canManageProperties &&
                                $vacantUnits > 0
                            );

                    @endphp


                    {{-- Outstanding Rent --}}

                    @if(
                        $canManageFinance &&
                        count($attentionCurrencies) > 0
                    )

                        <a
                            href="{{ route('rent-invoices.index') }}"
                            class="attention-link"
                        >

                            <div class="attention-left">

                                <div class="attention-icon danger">
                                    <i class="bi bi-cash-stack"></i>
                                </div>

                                <div>

                                    <div class="attention-title">
                                        Outstanding Rent
                                    </div>

                                    <div class="attention-description">

                                        @foreach(
                                            $attentionCurrencies
                                            as $currency => $amount
                                        )

                                            <span>
                                                {{
                                                    $formatCurrency(
                                                        $amount,
                                                        $currency
                                                    )
                                                }}
                                            </span>

                                            @if(!$loop->last)

                                                <span> · </span>

                                            @endif

                                        @endforeach

                                        <span>
                                            currently outstanding
                                        </span>

                                    </div>

                                </div>

                            </div>

                            <i
                                class="bi bi-chevron-right attention-arrow"
                            ></i>

                        </a>

                    @endif


                    {{-- Overdue Invoices --}}

                    @if(
                        $canManageFinance &&
                        $overdueInvoicesCount > 0
                    )

                        <a
                            href="{{ route('rent-invoices.index') }}"
                            class="attention-link"
                        >

                            <div class="attention-left">

                                <div class="attention-icon danger">
                                    <i class="bi bi-clock-history"></i>
                                </div>

                                <div>

                                    <div class="attention-title">
                                        Overdue Invoices
                                    </div>

                                    <div class="attention-description">

                                        {{ number_format(
                                            $overdueInvoicesCount
                                        ) }}

                                        invoices are overdue

                                    </div>

                                </div>

                            </div>

                            <i
                                class="bi bi-chevron-right attention-arrow"
                            ></i>

                        </a>

                    @endif


                    {{-- Maintenance --}}

                    @if(
                        $canManageMaintenance &&
                        $openMaintenanceRequests > 0
                    )

                        <a
                            href="{{ route(
                                'maintenance-requests.index'
                            ) }}"
                            class="attention-link"
                        >

                            <div class="attention-left">

                                <div class="attention-icon">

                                    <i class="bi bi-tools"></i>

                                </div>

                                <div>

                                    <div class="attention-title">
                                        Maintenance Requests
                                    </div>

                                    <div class="attention-description">

                                        {{ number_format(
                                            $openMaintenanceRequests
                                        ) }}

                                        open requests

                                    </div>

                                </div>

                            </div>

                            <i
                                class="bi bi-chevron-right attention-arrow"
                            ></i>

                        </a>

                    @endif


                    {{-- Vacant Units --}}

                    @if(
                        $canManageProperties &&
                        $vacantUnits > 0
                    )

                        <a
                            href="{{ route('units.index') }}"
                            class="attention-link"
                        >

                            <div class="attention-left">

                                <div class="attention-icon blue">

                                    <i class="bi bi-door-open"></i>

                                </div>

                                <div>

                                    <div class="attention-title">
                                        Vacant Units
                                    </div>

                                    <div class="attention-description">

                                        {{ number_format(
                                            $vacantUnits
                                        ) }}

                                        units currently vacant

                                    </div>

                                </div>

                            </div>

                            <i
                                class="bi bi-chevron-right attention-arrow"
                            ></i>

                        </a>

                    @endif


                    @if(!$hasAttentionItems)

                        <div class="dashboard-empty">

                            <div class="dashboard-empty-icon">

                                <i class="bi bi-check-circle"></i>

                            </div>

                            <div class="dashboard-empty-text">

                                No immediate items require attention.

                            </div>

                        </div>

                    @endif

                </div>

            </div>


            {{-- ========================================================
                 RECENT PAYMENTS
            ========================================================= --}}

            @if($canManageFinance)

                <div class="dashboard-card">

                    <div class="dashboard-card-header">

                        <div>

                            <h2 class="dashboard-card-title">
                                Recent Payments
                            </h2>

                            <p class="dashboard-card-subtitle">
                                Latest recorded rent payments
                            </p>

                        </div>

                        <a
                            href="{{ route('payments.index') }}"
                            class="dashboard-card-link"
                        >
                            View All
                        </a>

                    </div>

                    @if($recentPayments->count() > 0)

                        <div class="dashboard-list">

                            @foreach($recentPayments as $payment)

                                @php

                                    $paymentCurrency =
                                        strtoupper(
                                            trim(
                                                (string)
                                                $payment->currency
                                            )
                                        );

                                    $paymentCurrencySupported =
                                        in_array(
                                            $paymentCurrency,
                                            Currency::SUPPORTED,
                                            true
                                        );

                                @endphp

                                <div class="dashboard-list-item">

                                    <div class="dashboard-list-icon">

                                        <i class="bi bi-cash-stack"></i>

                                    </div>

                                    <div class="dashboard-list-content">

                                        <div class="dashboard-list-title">

                                            {{
                                                $payment
                                                    ->rentInvoice
                                                    ?->lease
                                                    ?->tenant
                                                    ?->name
                                                ?? 'Tenant'
                                            }}

                                        </div>

                                        <div class="dashboard-list-meta">

                                            @if(
                                                $payment
                                                    ->rentInvoice
                                                    ?->lease
                                                    ?->unit
                                            )

                                                Unit

                                                {{
                                                    $payment
                                                        ->rentInvoice
                                                        ->lease
                                                        ->unit
                                                        ->unit_number
                                                    ??
                                                    $payment
                                                        ->rentInvoice
                                                        ->lease
                                                        ->unit
                                                        ->id
                                                }}

                                            @endif

                                            @if($payment->created_at)

                                                ·

                                                {{
                                                    $payment
                                                        ->created_at
                                                        ->format('d M Y')
                                                }}

                                            @endif

                                        </div>

                                    </div>

                                    <div class="dashboard-list-value">

                                        @if($paymentCurrencySupported)

                                            {{
                                                $formatCurrency(
                                                    $payment->amount,
                                                    $paymentCurrency
                                                )
                                            }}

                                        @else

                                            <span
                                                title="
                                                    Payment has an unsupported
                                                    or missing currency.
                                                "
                                            >

                                                {{
                                                    number_format(
                                                        (float)
                                                        $payment->amount,
                                                        2
                                                    )
                                                }}

                                            </span>

                                        @endif

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    @else

                        <div class="dashboard-empty">

                            <div class="dashboard-empty-icon">
                                <i class="bi bi-cash-stack"></i>
                            </div>

                            <div class="dashboard-empty-text">
                                No payments have been recorded yet.
                            </div>

                        </div>

                    @endif

                </div>

            @endif


            {{-- ========================================================
                 RECENT MAINTENANCE
            ========================================================= --}}

            @if($canManageMaintenance)

                <div class="dashboard-card">

                    <div class="dashboard-card-header">

                        <div>

                            <h2 class="dashboard-card-title">
                                Recent Maintenance
                            </h2>

                            <p class="dashboard-card-subtitle">
                                Latest maintenance activity
                            </p>

                        </div>

                        <a
                            href="{{ route(
                                'maintenance-requests.index'
                            ) }}"
                            class="dashboard-card-link"
                        >
                            View All
                        </a>

                    </div>

                    @if($recentMaintenanceRequests->count() > 0)

                        <div class="dashboard-list">

                            @foreach(
                                $recentMaintenanceRequests
                                as $request
                            )

                                <div class="dashboard-list-item">

                                    <div class="dashboard-list-icon">

                                        <i class="bi bi-tools"></i>

                                    </div>

                                    <div class="dashboard-list-content">

                                        <div class="dashboard-list-title">

                                            {{
                                                $request->title
                                                ??
                                                $request->subject
                                                ??
                                                'Maintenance Request'
                                            }}

                                        </div>

                                        <div class="dashboard-list-meta">

                                            @if($request->status)

                                                {{
                                                    ucwords(
                                                        str_replace(
                                                            '_',
                                                            ' ',
                                                            $request->status
                                                        )
                                                    )
                                                }}

                                            @endif

                                            @if($request->created_at)

                                                ·

                                                {{
                                                    $request
                                                        ->created_at
                                                        ->format('d M Y')
                                                }}

                                            @endif

                                        </div>

                                    </div>

                                    @if($request->status)

                                        <span
                                            class="
                                                dashboard-badge
                                                dashboard-badge-info
                                            "
                                        >

                                            {{
                                                ucwords(
                                                    str_replace(
                                                        '_',
                                                        ' ',
                                                        $request->status
                                                    )
                                                )
                                            }}

                                        </span>

                                    @endif

                                </div>

                            @endforeach

                        </div>

                    @else

                        <div class="dashboard-empty">

                            <div class="dashboard-empty-icon">

                                <i class="bi bi-tools"></i>

                            </div>

                            <div class="dashboard-empty-text">

                                No maintenance requests have been
                                recorded yet.

                            </div>

                        </div>

                    @endif

                </div>

            @endif


            {{-- ========================================================
                 PORTFOLIO SNAPSHOT
            ========================================================= --}}

            <div class="dashboard-card">

                <div class="dashboard-card-header">

                    <div>

                        <h2 class="dashboard-card-title">
                            Portfolio Snapshot
                        </h2>

                        <p class="dashboard-card-subtitle">
                            Current operational totals
                        </p>

                    </div>

                </div>

                <div class="dashboard-card-body">

                    <div class="finance-grid">

                        <div class="finance-item">

                            <div class="finance-item-label">
                                Properties
                            </div>

                            <div class="finance-item-value">

                                {{ number_format(
                                    $totalProperties
                                ) }}

                            </div>

                        </div>


                        <div class="finance-item">

                            <div class="finance-item-label">
                                Units
                            </div>

                            <div class="finance-item-value">

                                {{ number_format(
                                    $totalUnits
                                ) }}

                            </div>

                        </div>


                        <div class="finance-item">

                            <div class="finance-item-label">
                                Occupied
                            </div>

                            <div
                                class="
                                    finance-item-value
                                    positive
                                "
                            >

                                {{ number_format(
                                    $occupiedUnits
                                ) }}

                            </div>

                        </div>


                        <div class="finance-item">

                            <div class="finance-item-label">
                                Vacant
                            </div>

                            <div
                                class="
                                    finance-item-value
                                    {{
                                        $vacantUnits > 0
                                            ? 'warning'
                                            : 'positive'
                                    }}
                                "
                            >

                                {{ number_format(
                                    $vacantUnits
                                ) }}

                            </div>

                        </div>


                        <div class="finance-item">

                            <div class="finance-item-label">
                                Active Tenants
                            </div>

                            <div class="finance-item-value">

                                {{ number_format(
                                    $activeTenants
                                ) }}

                            </div>

                        </div>


                        <div class="finance-item">

                            <div class="finance-item-label">
                                Occupancy
                            </div>

                            <div class="finance-item-value">

                                {{ number_format(
                                    $occupancyRate,
                                    1
                                ) }}%

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const revenueTrendData = @json($monthlyRevenueTrend ?? []);
    const occupancyData = @json($occupancyTrend ?? []);
    const expenseData = @json($expenseDistribution ?? []);
    const totalUnits = {{ (int) $totalUnits }};
    const occupiedUnits = {{ (int) $occupiedUnits }};
    const vacantUnits = {{ (int) $vacantUnits }};

    let activeCurrency = "{{ $supportedCurrencies[0] ?? 'USD' }}";

    // Chart instances
    let revenueChart = null;
    let occupancyChart = null;
    let unitStatusChart = null;
    let expenseChart = null;

    // 1. Revenue & Collection Chart
    function renderRevenueChart(currency) {
        const canvas = document.getElementById('dashboardRevenueChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');

        const currData = revenueTrendData[currency] || { labels: [], invoiced: [], collected: [] };

        if (revenueChart) {
            revenueChart.destroy();
        }

        revenueChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: currData.labels,
                datasets: [
                    {
                        label: 'Collected (' + currency + ')',
                        data: currData.collected,
                        backgroundColor: 'rgba(13, 148, 136, 0.85)',
                        borderColor: '#0d9488',
                        borderWidth: 1,
                        borderRadius: 6,
                    },
                    {
                        label: 'Invoiced (' + currency + ')',
                        data: currData.invoiced,
                        backgroundColor: 'rgba(37, 99, 235, 0.35)',
                        borderColor: '#2563eb',
                        borderWidth: 2,
                        type: 'line',
                        tension: 0.3,
                        fill: false,
                        pointBackgroundColor: '#2563eb'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { font: { family: 'inherit', size: 12 } } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + Number(context.raw).toLocaleString(undefined, {minimumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(226, 232, 240, 0.6)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // 2. Occupancy Trend Chart (Line)
    function renderOccupancyChart() {
        const canvas = document.getElementById('dashboardOccupancyChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');

        if (occupancyChart) {
            occupancyChart.destroy();
        }

        occupancyChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: occupancyData.labels || [],
                datasets: [{
                    label: 'Occupancy Rate (%)',
                    data: occupancyData.rate || [],
                    borderColor: '#0d9488',
                    backgroundColor: 'rgba(13, 148, 136, 0.1)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#0d9488'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return 'Occupancy: ' + ctx.raw + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        min: 0,
                        max: 100,
                        ticks: { callback: v => v + '%' },
                        grid: { color: 'rgba(226, 232, 240, 0.6)' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // 3. Unit Status Doughnut Chart
    function renderUnitStatusChart() {
        const canvas = document.getElementById('dashboardUnitStatusChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');

        if (unitStatusChart) {
            unitStatusChart.destroy();
        }

        unitStatusChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Occupied Units', 'Vacant Units'],
                datasets: [{
                    data: [occupiedUnits, vacantUnits],
                    backgroundColor: ['#0d9488', '#f59e0b'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12 } }
                }
            }
        });
    }

    // 4. Expense Distribution Chart
    function renderExpenseChart(currency) {
        const canvas = document.getElementById('dashboardExpenseChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');

        const currExp = expenseData[currency] || { labels: [], values: [] };

        if (expenseChart) {
            expenseChart.destroy();
        }

        const expBadge = document.getElementById('expenseCurrencyBadge');
        if (expBadge) expBadge.textContent = currency;

        expenseChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: currExp.labels,
                datasets: [{
                    data: currExp.values,
                    backgroundColor: [
                        '#0d9488',
                        '#2563eb',
                        '#d97706',
                        '#dc2626',
                        '#8b5cf6',
                        '#06b6d4'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ctx.label + ': ' + Number(ctx.raw).toLocaleString(undefined, {minimumFractionDigits: 2}) + ' ' + currency;
                            }
                        }
                    }
                }
            }
        });
    }

    // Initial renders
    renderRevenueChart(activeCurrency);
    renderOccupancyChart();
    renderUnitStatusChart();
    renderExpenseChart(activeCurrency);

    // Global switch function for pill buttons
    window.switchRevenueCurrency = function(curr, btn) {
        activeCurrency = curr;
        document.querySelectorAll('.currency-pill').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');
        renderRevenueChart(curr);
        renderExpenseChart(curr);
    };
});
</script>
@endpush