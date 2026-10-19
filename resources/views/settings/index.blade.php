@extends('layouts.app')

@section('title', 'Settings')
@section('page-heading', 'Settings')

@push('styles')
<style>
    .settings-page {
        max-width: 1220px;
        margin: 0 auto;
        padding-bottom: 2rem;
    }

    /* =========================================================
       HEADER
       ========================================================= */

    .settings-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .settings-header-main {
        display: flex;
        align-items: center;
        gap: .9rem;
    }

    .settings-header-icon {
        width: 46px;
        height: 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 12px;
        background: rgba(13, 148, 136, .10);
        color: var(--pm-teal);
        font-size: 1.2rem;
    }

    .settings-header h2 {
        margin: 0;
        color: var(--pm-navy);
        font-size: 1.4rem;
        font-weight: 750;
        letter-spacing: -.02em;
    }

    .settings-header p {
        margin: .3rem 0 0;
        color: var(--pm-muted);
        font-size: .82rem;
    }

    .settings-header-badge {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .45rem .7rem;
        border: 1px solid var(--pm-border);
        border-radius: 999px;
        background: var(--pm-surface, #fff);
        color: var(--pm-muted);
        font-size: .7rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .settings-header-badge i {
        color: var(--pm-teal);
    }

    /* =========================================================
       ALERTS
       ========================================================= */

    .settings-alert {
        border-radius: 10px;
        border-width: 1px;
        font-size: .8rem;
    }

    /* =========================================================
       LAYOUT
       ========================================================= */

    .settings-layout {
        display: grid;
        grid-template-columns: 235px minmax(0, 1fr);
        gap: 1.35rem;
        align-items: start;
    }

    /* =========================================================
       SIDEBAR
       ========================================================= */

    .settings-menu {
        position: sticky;
        top: 90px;
        align-self: start;
        padding: .55rem;
        background: #fff;
        border: 1px solid var(--pm-border);
        border-radius: var(--pm-radius);
        box-shadow: var(--pm-shadow);
    }

    .settings-menu-title {
        padding: .65rem .75rem .5rem;
        color: #98a1af;
        font-size: .62rem;
        font-weight: 750;
        text-transform: uppercase;
        letter-spacing: .09em;
    }

    .settings-menu-link {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
        min-height: 42px;
        padding: .65rem .75rem;
        margin-bottom: .15rem;
        border-radius: 9px;
        color: #667085;
        font-size: .8rem;
        font-weight: 550;
        text-decoration: none;
        transition:
            background .18s ease,
            color .18s ease,
            transform .18s ease;
    }

    .settings-menu-link i {
        width: 23px;
        margin-right: .55rem;
        font-size: .95rem;
        text-align: center;
    }

    .settings-menu-link:hover {
        background: #f4f7f8;
        color: var(--pm-navy);
        transform: translateX(1px);
    }

    .settings-menu-link.active {
        background: rgba(13, 148, 136, .10);
        color: var(--pm-teal);
        font-weight: 650;
    }

    .settings-menu-link.active::before {
        content: "";
        position: absolute;
        left: 0;
        top: 9px;
        bottom: 9px;
        width: 3px;
        border-radius: 999px;
        background: var(--pm-teal);
    }

    .settings-content {
        min-width: 0;
    }

    /* =========================================================
       CARDS
       ========================================================= */

    .settings-card {
        position: relative;
        margin-bottom: 1.2rem;
        overflow: hidden;
        background: #fff;
        border: 1px solid var(--pm-border);
        border-radius: var(--pm-radius);
        box-shadow: var(--pm-shadow);
        scroll-margin-top: 95px;
    }

    .settings-card-header {
        display: flex;
        align-items: center;
        gap: .85rem;
        padding: 1.05rem 1.25rem;
        border-bottom: 1px solid var(--pm-border);
    }

    .settings-card-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 10px;
        background: rgba(13, 148, 136, .10);
        color: var(--pm-teal);
        font-size: 1rem;
    }

    .settings-card-title {
        margin: 0;
        color: var(--pm-navy);
        font-size: .92rem;
        font-weight: 700;
    }

    .settings-card-description {
        margin: .16rem 0 0;
        color: var(--pm-muted);
        font-size: .73rem;
        line-height: 1.45;
    }

    .settings-card-body {
        padding: 1.25rem;
    }

    /* =========================================================
       FORM CONTROLS
       ========================================================= */

    .settings-label {
        display: block;
        margin-bottom: .42rem;
        color: var(--pm-navy);
        font-size: .76rem;
        font-weight: 650;
    }

    .settings-control {
        min-height: 43px;
        border-color: var(--pm-border);
        border-radius: 9px;
        color: var(--pm-navy);
        font-size: .81rem;
        background-color: #fff;
        transition:
            border-color .18s ease,
            box-shadow .18s ease;
    }

    .settings-control:hover {
        border-color: #c8d0d8;
    }

    .settings-control:focus {
        border-color: var(--pm-teal);
        box-shadow: 0 0 0 .2rem rgba(13, 148, 136, .10);
    }

    .settings-control:disabled {
        background: #f7f8fa;
        color: #7d8794;
        opacity: 1;
    }

    .settings-help {
        margin-top: .4rem;
        color: #8a93a3;
        font-size: .68rem;
        line-height: 1.45;
    }

    /* =========================================================
       NOTIFICATION ROWS
       ========================================================= */

    .settings-row {
        padding: 1rem 0;
        border-bottom: 1px solid #edf0f2;
    }

    .settings-row:first-child {
        padding-top: 0;
    }

    .settings-row:last-child {
        padding-bottom: 0;
        border-bottom: 0;
    }

    .settings-row-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .settings-row-title {
        color: var(--pm-navy);
        font-size: .81rem;
        font-weight: 650;
    }

    .settings-row-description {
        max-width: 620px;
        margin-top: .2rem;
        color: var(--pm-muted);
        font-size: .71rem;
        line-height: 1.45;
    }

    .settings-switch {
        width: 43px !important;
        height: 23px !important;
        margin: 0 !important;
        cursor: pointer;
        flex-shrink: 0;
    }

    .settings-switch:focus {
        box-shadow: 0 0 0 .2rem rgba(13, 148, 136, .10);
    }

    /* =========================================================
       INFO ALERT
       ========================================================= */

    .settings-info {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        padding: .85rem .95rem;
        border: 1px solid #cfe8e5;
        border-radius: 10px;
        background: #f1faf9;
        color: #356966;
    }

    .settings-info-icon {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 8px;
        background: rgba(13, 148, 136, .10);
        color: var(--pm-teal);
    }

    .settings-info-title {
        margin-bottom: .15rem;
        color: #275a57;
        font-size: .76rem;
        font-weight: 700;
    }

    .settings-info-text {
        color: #537774;
        font-size: .7rem;
        line-height: 1.5;
    }

    /* =========================================================
       SECURITY
       ========================================================= */

    .settings-security-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: .95rem;
        background: #fafbfc;
        border: 1px solid var(--pm-border);
        border-radius: 10px;
        color: var(--pm-navy);
        text-decoration: none;
        transition:
            border-color .18s ease,
            background .18s ease,
            transform .18s ease;
    }

    .settings-security-link:hover {
        background: #f5f7f9;
        border-color: #cfd6dd;
        color: var(--pm-navy);
        transform: translateY(-1px);
    }

    .settings-security-left {
        display: flex;
        align-items: center;
        gap: .75rem;
        min-width: 0;
    }

    .settings-security-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 9px;
        background: #f0f2f5;
        color: var(--pm-navy);
    }

    .settings-security-title {
        color: var(--pm-navy);
        font-size: .78rem;
        font-weight: 650;
    }

    .settings-security-description {
        margin-top: .18rem;
        color: var(--pm-muted);
        font-size: .69rem;
    }

    .settings-security-arrow {
        color: #9aa3ae;
        font-size: .85rem;
    }

    /* =========================================================
       SYSTEM INFORMATION
       ========================================================= */

    .system-info {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .75rem;
    }

    .system-info-item {
        padding: .9rem;
        background: #f8f9fb;
        border: 1px solid #edf0f3;
        border-radius: 10px;
    }

    .system-info-label {
        display: flex;
        align-items: center;
        gap: .4rem;
        color: #8a93a3;
        font-size: .66rem;
        font-weight: 650;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .system-info-label i {
        font-size: .72rem;
    }

    .system-info-value {
        margin-top: .35rem;
        color: var(--pm-navy);
        font-size: .78rem;
        font-weight: 650;
        overflow-wrap: anywhere;
    }

    .system-env {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .2rem .45rem;
        border-radius: 999px;
        background: rgba(13, 148, 136, .09);
        color: var(--pm-teal);
        font-size: .66rem;
        font-weight: 650;
    }

    /* =========================================================
       ACTIONS
       ========================================================= */

    .settings-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: .6rem;
        padding: .25rem 0 1rem;
    }

    .settings-actions .btn {
        min-height: 40px;
        border-radius: 9px;
        font-size: .78rem;
        font-weight: 650;
    }

    .settings-save-btn {
        min-width: 125px;
    }

    .settings-save-btn .spinner-border {
        width: .85rem;
        height: .85rem;
        border-width: .13em;
    }

    /* =========================================================
       DARK MODE
       ========================================================= */

    html[data-theme="dark"] .settings-menu,
    html[data-theme="dark"] .settings-card,
    html[data-theme="dark"] .settings-header-badge {
        background: #151b23;
        border-color: #29313d;
    }

    html[data-theme="dark"] .settings-header h2,
    html[data-theme="dark"] .settings-card-title,
    html[data-theme="dark"] .settings-label,
    html[data-theme="dark"] .settings-row-title,
    html[data-theme="dark"] .system-info-value,
    html[data-theme="dark"] .settings-security-title,
    html[data-theme="dark"] .settings-security-link {
        color: #e7ebf0;
    }

    html[data-theme="dark"] .settings-menu-link {
        color: #aeb7c4;
    }

    html[data-theme="dark"] .settings-menu-link:hover {
        background: #1d2530;
        color: #fff;
    }

    html[data-theme="dark"] .settings-menu-link.active {
        background: rgba(13, 148, 136, .15);
        color: #45c7bc;
    }

    html[data-theme="dark"] .settings-card-header,
    html[data-theme="dark"] .settings-row {
        border-color: #29313d;
    }

    html[data-theme="dark"] .settings-control {
        background-color: #11161d;
        border-color: #303947;
        color: #e7ebf0;
    }

    html[data-theme="dark"] .settings-control:disabled {
        background: #1b222c;
        color: #8993a1;
    }

    html[data-theme="dark"] .settings-control:hover {
        border-color: #414c5b;
    }

    html[data-theme="dark"] .settings-help,
    html[data-theme="dark"] .settings-row-description {
        color: #8f9aaa;
    }

    html[data-theme="dark"] .settings-security-link {
        background: #11161d;
        border-color: #303947;
    }

    html[data-theme="dark"] .settings-security-link:hover {
        background: #1a212b;
        border-color: #414c5b;
    }

    html[data-theme="dark"] .settings-security-icon,
    html[data-theme="dark"] .system-info-item {
        background: #1b222c;
        border-color: #303947;
    }

    html[data-theme="dark"] .system-info-label {
        color: #8993a1;
    }

    html[data-theme="dark"] .settings-info {
        background: rgba(13, 148, 136, .08);
        border-color: rgba(69, 199, 188, .2);
    }

    html[data-theme="dark"] .settings-info-title {
        color: #76d8d0;
    }

    html[data-theme="dark"] .settings-info-text {
        color: #9dc6c2;
    }

    html[data-theme="dark"] .settings-header-badge {
        color: #aeb7c4;
    }

    /* =========================================================
       RESPONSIVE
       ========================================================= */

    @media (max-width: 991.98px) {
        .settings-layout {
            grid-template-columns: 1fr;
        }

        .settings-menu {
            position: sticky;
            top: 70px;
            z-index: 10;
            padding: .45rem;
        }

        .settings-menu-title {
            display: none;
        }

        .settings-menu-links {
            display: flex;
            overflow-x: auto;
            gap: .2rem;
            scrollbar-width: thin;
        }

        .settings-menu-link {
            width: auto;
            min-width: max-content;
            margin-bottom: 0;
            white-space: nowrap;
        }

        .settings-menu-link.active::before {
            top: auto;
            left: 10px;
            right: 10px;
            bottom: 3px;
            width: auto;
            height: 2px;
        }
    }

    @media (max-width: 767.98px) {
        .settings-header {
            align-items: flex-start;
        }

        .settings-header-badge {
            display: none;
        }

        .settings-card-body {
            padding: 1rem;
        }

        .settings-card-header {
            padding: 1rem;
        }
    }

    @media (max-width: 575.98px) {
        .settings-header-main {
            align-items: flex-start;
        }

        .settings-header-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
        }

        .settings-header h2 {
            font-size: 1.2rem;
        }

        .settings-header p {
            font-size: .76rem;
        }

        .system-info {
            grid-template-columns: 1fr;
        }

        .settings-row-content {
            align-items: flex-start;
        }

        .settings-actions {
            flex-direction: column-reverse;
            align-items: stretch;
        }

        .settings-actions .btn {
            width: 100%;
        }

        .settings-save-btn {
            width: 100%;
        }
    }
</style>
@endpush


@section('content')

<div class="settings-page">

    {{-- =========================================================
         PAGE HEADER
         ========================================================= --}}

    <div class="settings-header">

        <div class="settings-header-main">

            <div class="settings-header-icon">
                <i class="bi bi-gear-wide-connected"></i>
            </div>

            <div>
                <h2>System Settings</h2>

                <p>
                    Manage your application preferences and account configuration.
                </p>
            </div>

        </div>

        <div class="settings-header-badge">
            <i class="bi bi-shield-check"></i>
            Configuration
        </div>

    </div>


    {{-- =========================================================
         SUCCESS MESSAGE
         ========================================================= --}}

    @if(session('success'))
        <div class="alert alert-success settings-alert d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-check-circle-fill"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif


    {{-- =========================================================
         VALIDATION ERRORS
         ========================================================= --}}

    @if($errors->any())
        <div class="alert alert-danger settings-alert mb-4">

            <div class="d-flex align-items-start gap-2">

                <i class="bi bi-exclamation-triangle-fill mt-1"></i>

                <div>
                    <div class="fw-semibold mb-1">
                        Please review the following:
                    </div>

                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>

            </div>

        </div>
    @endif


    <div class="settings-layout">

        {{-- =====================================================
             SETTINGS NAVIGATION
             ===================================================== --}}

        <aside class="settings-menu">

            <div class="settings-menu-title">
                Settings
            </div>

            <div class="settings-menu-links">

                <a
                    href="#general"
                    class="settings-menu-link active"
                >
                    <i class="bi bi-sliders"></i>
                    General
                </a>

                <a
                    href="#localization"
                    class="settings-menu-link"
                >
                    <i class="bi bi-globe2"></i>
                    Language & Currency
                </a>

                <a
                    href="#notifications"
                    class="settings-menu-link"
                >
                    <i class="bi bi-bell"></i>
                    Notifications
                </a>

                <a
                    href="#appearance"
                    class="settings-menu-link"
                >
                    <i class="bi bi-palette"></i>
                    Appearance
                </a>

                <a
                    href="#security"
                    class="settings-menu-link"
                >
                    <i class="bi bi-shield-lock"></i>
                    Security
                </a>

                <a
                    href="#system"
                    class="settings-menu-link"
                >
                    <i class="bi bi-info-circle"></i>
                    System
                </a>

            </div>

        </aside>


        {{-- =====================================================
             SETTINGS CONTENT
             ===================================================== --}}

        <div class="settings-content">

            <form
                method="POST"
                action="{{ route('settings.update') }}"
                id="settings-form"
            >

                @csrf
                @method('PATCH')


                {{-- =================================================
                     GENERAL
                     ================================================= --}}

                <section
                    id="general"
                    class="settings-card"
                >

                    <div class="settings-card-header">

                        <div class="settings-card-icon">
                            <i class="bi bi-sliders"></i>
                        </div>

                        <div>
                            <h5 class="settings-card-title">
                                General
                            </h5>

                            <p class="settings-card-description">
                                Basic application preferences.
                            </p>
                        </div>

                    </div>


                    <div class="settings-card-body">

                        <div class="row g-4">

                            <div class="col-md-6">

                                <label class="settings-label">
                                    Application Name
                                </label>

                                <input
                                    type="text"
                                    class="form-control settings-control"
                                    value="ImmoFlow"
                                    disabled
                                >

                                <div class="settings-help">
                                    Application branding is configured by the administrator.
                                </div>

                            </div>


                            <div class="col-md-6">

                                <label
                                    for="timezone"
                                    class="settings-label"
                                >
                                    Timezone
                                </label>

                                <select
                                    name="timezone"
                                    id="timezone"
                                    class="form-select settings-control"
                                >

                                    <option
                                        value="Africa/Nairobi"
                                        @selected(old('timezone', $timezone ?? null) === 'Africa/Nairobi')
                                    >
                                        Africa/Nairobi (EAT)
                                    </option>

                                    <option
                                        value="UTC"
                                        @selected(old('timezone', $timezone ?? null) === 'UTC')
                                    >
                                        UTC
                                    </option>

                                    <option
                                        value="Africa/Kampala"
                                        @selected(old('timezone', $timezone ?? null) === 'Africa/Kampala')
                                    >
                                        Africa/Kampala
                                    </option>

                                    <option
                                        value="Africa/Dar_es_Salaam"
                                        @selected(old('timezone', $timezone ?? null) === 'Africa/Dar_es_Salaam')
                                    >
                                        Africa/Dar es Salaam
                                    </option>

                                </select>

                                <div class="settings-help">
                                    Used when displaying dates and times throughout the system.
                                </div>

                            </div>

                        </div>

                    </div>

                </section>


                {{-- =================================================
                     LANGUAGE & CURRENCY
                     ================================================= --}}

                @php
                    $selectedLanguage = old('language', $language ?? null);
                    $selectedCurrency = old('currency', $currency ?? null);
                @endphp

                <section
                    id="localization"
                    class="settings-card"
                >

                    <div class="settings-card-header">

                        <div class="settings-card-icon">
                            <i class="bi bi-globe2"></i>
                        </div>

                        <div>
                            <h5 class="settings-card-title">
                                Language & Currency
                            </h5>

                            <p class="settings-card-description">
                                Choose how information is displayed throughout the system.
                            </p>
                        </div>

                    </div>


                    <div class="settings-card-body">

                        <div class="row g-4">

                            <div class="col-md-6">

                                <label
                                    for="language"
                                    class="settings-label"
                                >
                                    Language
                                </label>

                                <select
                                    name="language"
                                    id="language"
                                    class="form-select settings-control"
                                >

                                    <option
                                        value="en"
                                        @selected($selectedLanguage === 'en')
                                    >
                                        English
                                    </option>

                                    <option
                                        value="sw"
                                        @selected($selectedLanguage === 'sw')
                                    >
                                        Kiswahili
                                    </option>

                                    <option
                                        value="fr"
                                        @selected($selectedLanguage === 'fr')
                                    >
                                        Français
                                    </option>

                                </select>

                                <div class="settings-help">
                                    Select the language used by the application interface.
                                </div>

                            </div>


                            <div class="col-md-6">

                                <label
                                    for="currency"
                                    class="settings-label"
                                >
                                    Default Currency
                                </label>

                                <select
                                    name="currency"
                                    id="currency"
                                    class="form-select settings-control"
                                >

                                    <option
                                        value="USD"
                                        @selected($selectedCurrency === 'USD')
                                    >
                                        USD — US Dollar
                                    </option>

                                    <option
                                        value="KES"
                                        @selected($selectedCurrency === 'KES')
                                    >
                                        KES — Kenyan Shilling
                                    </option>

                                    <option
                                        value="CDF"
                                        @selected($selectedCurrency === 'CDF')
                                    >
                                        CDF — Congolese Franc
                                    </option>

                                </select>

                                <div class="settings-help">
                                    Supported currencies are USD, KES and CDF.
                                </div>

                            </div>

                        </div>


                        <div class="settings-info mt-4">

                            <div class="settings-info-icon">
                                <i class="bi bi-info-circle-fill"></i>
                            </div>

                            <div>

                                <div class="settings-info-title">
                                    Currency planning
                                </div>

                                <div class="settings-info-text">
                                    Currency settings control the application's default display preference.
                                    Existing invoices, payments and financial records retain the currency
                                    recorded when they were created.
                                </div>

                            </div>

                        </div>

                    </div>

                </section>


                {{-- =================================================
                     NOTIFICATIONS
                     ================================================= --}}

                <section
                    id="notifications"
                    class="settings-card"
                >

                    <div class="settings-card-header">

                        <div class="settings-card-icon">
                            <i class="bi bi-bell"></i>
                        </div>

                        <div>
                            <h5 class="settings-card-title">
                                Notifications
                            </h5>

                            <p class="settings-card-description">
                                Choose which system notifications you receive.
                            </p>
                        </div>

                    </div>


                    <div class="settings-card-body">

                        <div class="settings-row">

                            <div class="settings-row-content">

                                <div>
                                    <div class="settings-row-title">
                                        Email Notifications
                                    </div>

                                    <div class="settings-row-description">
                                        Receive important system notifications by email.
                                    </div>
                                </div>

                                <div class="form-check form-switch">

                                    <input
                                        type="hidden"
                                        name="email_notifications"
                                        value="0"
                                    >

                                    <input
                                        class="form-check-input settings-switch"
                                        type="checkbox"
                                        name="email_notifications"
                                        value="1"
                                        @checked(old('email_notifications', $emailNotifications ?? true))
                                    >

                                </div>

                            </div>

                        </div>


                        <div class="settings-row">

                            <div class="settings-row-content">

                                <div>
                                    <div class="settings-row-title">
                                        Maintenance Notifications
                                    </div>

                                    <div class="settings-row-description">
                                        Get notified about new and updated maintenance requests.
                                    </div>
                                </div>

                                <div class="form-check form-switch">

                                    <input
                                        type="hidden"
                                        name="maintenance_notifications"
                                        value="0"
                                    >

                                    <input
                                        class="form-check-input settings-switch"
                                        type="checkbox"
                                        name="maintenance_notifications"
                                        value="1"
                                        @checked(old('maintenance_notifications', $maintenanceNotifications ?? true))
                                    >

                                </div>

                            </div>

                        </div>


                        <div class="settings-row">

                            <div class="settings-row-content">

                                <div>
                                    <div class="settings-row-title">
                                        Payment Notifications
                                    </div>

                                    <div class="settings-row-description">
                                        Receive notifications when payments are recorded.
                                    </div>
                                </div>

                                <div class="form-check form-switch">

                                    <input
                                        type="hidden"
                                        name="payment_notifications"
                                        value="0"
                                    >

                                    <input
                                        class="form-check-input settings-switch"
                                        type="checkbox"
                                        name="payment_notifications"
                                        value="1"
                                        @checked(old('payment_notifications', $paymentNotifications ?? true))
                                    >

                                </div>

                            </div>

                        </div>

                    </div>

                </section>


                {{-- =================================================
                     APPEARANCE
                     ================================================= --}}

                @php
                    $selectedAppearance = old('appearance', $appearance ?? null);
                @endphp

                <section
                    id="appearance"
                    class="settings-card"
                >

                    <div class="settings-card-header">

                        <div class="settings-card-icon">
                            <i class="bi bi-palette"></i>
                        </div>

                        <div>
                            <h5 class="settings-card-title">
                                Appearance
                            </h5>

                            <p class="settings-card-description">
                                Customize how the management system looks.
                            </p>
                        </div>

                    </div>


                    <div class="settings-card-body">

                        <label
                            for="theme"
                            class="settings-label"
                        >
                            Theme
                        </label>

                        <select
                            name="appearance"
                            id="theme"
                            class="form-select settings-control"
                        >

                            <option
                                value="light"
                                @selected($selectedAppearance === 'light')
                            >
                                Light
                            </option>

                            <option
                                value="dark"
                                @selected($selectedAppearance === 'dark')
                            >
                                Dark
                            </option>

                            <option
                                value="system"
                                @selected($selectedAppearance === 'system')
                            >
                                Use system preference
                            </option>

                        </select>

                        <div class="settings-help">
                            Choose light mode, dark mode, or automatically follow your device preference.
                        </div>

                    </div>

                </section>


                {{-- =================================================
                     SECURITY
                     ================================================= --}}

                <section
                    id="security"
                    class="settings-card"
                >

                    <div class="settings-card-header">

                        <div class="settings-card-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>

                        <div>
                            <h5 class="settings-card-title">
                                Security
                            </h5>

                            <p class="settings-card-description">
                                Manage your account security and profile information.
                            </p>
                        </div>

                    </div>


                    <div class="settings-card-body">

                        <a
                            href="{{ route('profile.edit') }}"
                            class="settings-security-link"
                        >

                            <div class="settings-security-left">

                                <div class="settings-security-icon">
                                    <i class="bi bi-person-lock"></i>
                                </div>

                                <div>
                                    <div class="settings-security-title">
                                        Password & Profile
                                    </div>

                                    <div class="settings-security-description">
                                        Update your personal information and password.
                                    </div>
                                </div>

                            </div>

                            <div class="settings-security-arrow">
                                <i class="bi bi-chevron-right"></i>
                            </div>

                        </a>

                    </div>

                </section>


                {{-- =================================================
                     SYSTEM INFORMATION
                     ================================================= --}}

                <section
                    id="system"
                    class="settings-card"
                >

                    <div class="settings-card-header">

                        <div class="settings-card-icon">
                            <i class="bi bi-info-circle"></i>
                        </div>

                        <div>
                            <h5 class="settings-card-title">
                                System Information
                            </h5>

                            <p class="settings-card-description">
                                Information about your application environment.
                            </p>
                        </div>

                    </div>


                    <div class="settings-card-body">

                        <div class="system-info">

                            <div class="system-info-item">

                                <div class="system-info-label">
                                    <i class="bi bi-grid-1x2"></i>
                                    Application
                                </div>

                                <div class="system-info-value">
                                    ImmoFlow
                                </div>

                            </div>


                            <div class="system-info-item">

                                <div class="system-info-label">
                                    <i class="bi bi-code-square"></i>
                                    Framework
                                </div>

                                <div class="system-info-value">
                                    Laravel
                                </div>

                            </div>


                            <div class="system-info-item">

                                <div class="system-info-label">
                                    <i class="bi bi-hdd-rack"></i>
                                    Environment
                                </div>

                                <div class="system-info-value">

                                    <span class="system-env">
                                        <i class="bi bi-circle-fill" style="font-size:.4rem;"></i>
                                        {{ app()->environment() }}
                                    </span>

                                </div>

                            </div>


                            <div class="system-info-item">

                                <div class="system-info-label">
                                    <i class="bi bi-person-circle"></i>
                                    Current User
                                </div>

                                <div class="system-info-value">
                                    {{ auth()->user()->email }}
                                </div>

                            </div>

                        </div>

                    </div>

                </section>


                {{-- =================================================
                     ACTIONS
                     ================================================= --}}

                <div class="settings-actions">

                    <button
                        type="reset"
                        class="btn pm-btn-outline"
                        id="settings-cancel"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="btn pm-btn-primary px-4 settings-save-btn"
                        id="settings-save"
                    >

                        <span class="settings-save-content">
                            <i class="bi bi-check-lg me-1"></i>
                            Save Changes
                        </span>

                        <span
                            class="settings-save-loading d-none"
                        >
                            <span
                                class="spinner-border me-2"
                                role="status"
                                aria-hidden="true"
                            ></span>
                            Saving...
                        </span>

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const links = document.querySelectorAll('.settings-menu-link');
        const sections = document.querySelectorAll('.settings-card[id]');
        const form = document.getElementById('settings-form');
        const saveButton = document.getElementById('settings-save');
        const saveContent = document.querySelector('.settings-save-content');
        const saveLoading = document.querySelector('.settings-save-loading');
        const cancelButton = document.getElementById('settings-cancel');

        /*
         * ---------------------------------------------------------
         * SETTINGS NAVIGATION
         * ---------------------------------------------------------
         */

        links.forEach(function (link) {

            link.addEventListener('click', function () {

                links.forEach(function (item) {
                    item.classList.remove('active');
                });

                this.classList.add('active');

            });

        });


        /*
         * ---------------------------------------------------------
         * ACTIVE SECTION WHILE SCROLLING
         * ---------------------------------------------------------
         */

        if ('IntersectionObserver' in window) {

            const observer = new IntersectionObserver(
                function (entries) {

                    entries.forEach(function (entry) {

                        if (!entry.isIntersecting) {
                            return;
                        }

                        const sectionId = entry.target.getAttribute('id');

                        links.forEach(function (link) {

                            const isActive =
                                link.getAttribute('href') === '#' + sectionId;

                            link.classList.toggle(
                                'active',
                                isActive
                            );

                        });

                    });

                },
                {
                    rootMargin: '-15% 0px -70% 0px',
                    threshold: 0
                }
            );

            sections.forEach(function (section) {
                observer.observe(section);
            });

        }


        /*
         * ---------------------------------------------------------
         * FORM SUBMIT STATE
         * ---------------------------------------------------------
         */

        if (form && saveButton) {

            form.addEventListener('submit', function () {

                saveButton.disabled = true;

                if (saveContent) {
                    saveContent.classList.add('d-none');
                }

                if (saveLoading) {
                    saveLoading.classList.remove('d-none');
                }

            });

        }


        /*
         * ---------------------------------------------------------
         * CANCEL / RESET
         * ---------------------------------------------------------
         */

        if (form && cancelButton) {

            cancelButton.addEventListener('click', function () {

                window.setTimeout(function () {
                    form.reset();
                }, 0);

            });

        }

    });
</script>
@endpush