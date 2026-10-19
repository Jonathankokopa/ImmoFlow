<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title', 'Tenant Portal')
        - {{ config('app.name', 'Real Estate System') }}
    </title>

    {{-- Apply saved dark mode before the page renders --}}
    <script>
        (function () {
            const savedAppearance = @json(
                auth()->user()->appearance ?? 'light'
            );

            if (savedAppearance === 'dark') {
                document.documentElement.classList.add(
                    'portal-dark-loading'
                );
            }
        })();
    </script>

    {{-- Bootstrap --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    {{-- Bootstrap Icons --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    <style>

        :root {
            --portal-primary: #0d6efd;
            --portal-navy: #172033;
            --portal-dark: #172033;
            --portal-sidebar: #ffffff;
            --portal-bg: #f5f7fb;
            --portal-border: #e5e7eb;
            --portal-text: #334155;
            --portal-muted: #64748b;
            --portal-topbar-height: 72px;
            --portal-card-bg: #ffffff;
            --portal-hover: #f8fafc;
            --portal-input-bg: #ffffff;
        }


        * {
            box-sizing: border-box;
        }


        html,
        body {
            margin: 0;
            min-height: 100%;
        }


        body {
            background: var(--portal-bg);

            color: var(--portal-text);

            font-family:
                Inter,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;

            transition:
                background-color .2s ease,
                color .2s ease;
        }


        a {
            text-decoration: none;
        }


        /*
        |--------------------------------------------------------------------------
        | Prevent flash when dark mode is saved
        |--------------------------------------------------------------------------
        */

        html.portal-dark-loading body {
            background: #0f172a;
        }


        /*
        |--------------------------------------------------------------------------
        | Dark Mode
        |--------------------------------------------------------------------------
        */

        html.portal-dark,
        html.portal-dark-loading {

            --portal-primary: #60a5fa;
            --portal-navy: #f8fafc;
            --portal-dark: #f8fafc;
            --portal-sidebar: #111827;
            --portal-bg: #0f172a;
            --portal-border: #263244;
            --portal-text: #e5e7eb;
            --portal-muted: #94a3b8;
            --portal-card-bg: #111827;
            --portal-hover: #1e293b;
            --portal-input-bg: #111827;
        }


        html.portal-dark body {
            background: var(--portal-bg);
            color: var(--portal-text);
        }


        /*
        |--------------------------------------------------------------------------
        | Wrapper
        |--------------------------------------------------------------------------
        */

        .portal-wrapper {
            min-height: 100vh;
        }


        /*
        |--------------------------------------------------------------------------
        | Sidebar
        |--------------------------------------------------------------------------
        */

        .portal-sidebar {
            width: 260px;

            min-height: 100vh;

            background: var(--portal-sidebar);

            border-right: 1px solid var(--portal-border);

            position: fixed;

            left: 0;
            top: 0;
            bottom: 0;

            z-index: 1000;

            overflow-y: auto;

            transition:
                background-color .2s ease,
                border-color .2s ease;
        }


        /*
        |--------------------------------------------------------------------------
        | Brand
        |--------------------------------------------------------------------------
        */

        .portal-brand {
            height: var(--portal-topbar-height);

            display: flex;

            align-items: center;

            padding: 0 1.1rem;

            flex-shrink: 0;

            background: var(--portal-sidebar);

            border-bottom: 1px solid var(--portal-border);
        }


        .portal-brand-link {
            width: 100%;

            display: flex;

            align-items: center;

            gap: .75rem;

            color: var(--portal-navy);

            text-decoration: none;
        }


        .portal-brand-link:hover {
            color: var(--portal-navy);
        }


        .portal-brand-icon {
            width: 40px;
            height: 40px;

            display: inline-flex;

            align-items: center;
            justify-content: center;

            flex-shrink: 0;

            border-radius: 11px;

            background: #172033;

            color: #ffffff;

            font-size: 1.15rem;
        }


        html.portal-dark .portal-brand-icon,
        html.portal-dark-loading .portal-brand-icon {
            background: #2563eb;
        }


        .portal-brand-text {
            display: flex;

            flex-direction: column;

            line-height: 1.15;

            white-space: nowrap;
        }


        .portal-brand-text strong {
            font-size: .95rem;

            font-weight: 700;
        }


        /*
        |--------------------------------------------------------------------------
        | Navigation
        |--------------------------------------------------------------------------
        */

        .portal-nav {
            padding: 20px 12px;
        }


        .portal-nav-label {
            font-size: 10px;

            letter-spacing: .08em;

            text-transform: uppercase;

            color: #94a3b8;

            font-weight: 700;

            padding: 0 14px;

            margin-bottom: 8px;
        }


        .portal-nav .nav-link {
            color: var(--portal-muted);

            border-radius: 10px;

            padding: 11px 14px;

            margin-bottom: 4px;

            display: flex;

            align-items: center;

            gap: 11px;

            transition: .15s ease;

            font-size: .95rem;
        }


        .portal-nav .nav-link i {
            width: 20px;

            text-align: center;

            font-size: 17px;
        }


        .portal-nav .nav-link:hover {
            background: var(--portal-hover);

            color: var(--portal-primary);
        }


        .portal-nav .nav-link.active {
            background: rgba(13, 110, 253, .10);

            color: var(--portal-primary);

            font-weight: 600;
        }


        .portal-logout {
            color: var(--portal-muted) !important;
        }


        .portal-logout:hover {
            background: rgba(220, 53, 69, .10) !important;

            color: #dc3545 !important;
        }


        /*
        |--------------------------------------------------------------------------
        | Main
        |--------------------------------------------------------------------------
        */

        .portal-main {
            margin-left: 260px;

            min-height: 100vh;
        }


        /*
        |--------------------------------------------------------------------------
        | Topbar
        |--------------------------------------------------------------------------
        */

        .portal-topbar {
            height: var(--portal-topbar-height);

            background: var(--portal-sidebar);

            border-bottom: 1px solid var(--portal-border);

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 30px;

            position: sticky;

            top: 0;

            z-index: 900;

            transition:
                background-color .2s ease,
                border-color .2s ease;
        }


        .portal-page-title {
            font-size: 18px;

            font-weight: 700;

            color: var(--portal-dark);

            margin: 0;
        }


        /*
        |--------------------------------------------------------------------------
        | Appearance Toggle
        |--------------------------------------------------------------------------
        */

        .portal-icon-button {
            width: 42px;
            height: 42px;

            border-radius: 10px;

            border: 1px solid var(--portal-border);

            background: var(--portal-card-bg);

            color: var(--portal-text);

            display: flex;

            align-items: center;
            justify-content: center;

            transition: .15s ease;

            cursor: pointer;
        }


        .portal-icon-button:hover {
            background: var(--portal-hover);

            color: var(--portal-primary);

            border-color: var(--portal-primary);
        }


        .portal-icon-button:focus {
            outline: none;

            box-shadow:
                0 0 0 .2rem rgba(13, 110, 253, .12);
        }


        .portal-icon-button i {
            font-size: 18px;
        }


        /*
        |--------------------------------------------------------------------------
        | Notification Bell
        |--------------------------------------------------------------------------
        */

        .portal-notification-button {
            width: 42px;
            height: 42px;

            border-radius: 10px;

            border: 1px solid var(--portal-border);

            background: var(--portal-card-bg);

            color: var(--portal-text);

            display: flex;

            align-items: center;
            justify-content: center;

            position: relative;

            transition: .15s ease;
        }


        .portal-notification-button:hover {
            background: var(--portal-hover);

            color: var(--portal-primary);
        }


        .portal-notification-button:focus {
            outline: none;

            box-shadow:
                0 0 0 .2rem rgba(13, 110, 253, .12);
        }


        .portal-notification-button i {
            font-size: 19px;
        }


        .portal-notification-badge {
            position: absolute;

            top: -5px;
            right: -5px;

            min-width: 19px;
            height: 19px;

            border-radius: 50%;

            background: #dc3545;

            color: #ffffff;

            font-size: 10px;

            font-weight: 700;

            display: flex;

            align-items: center;
            justify-content: center;

            padding: 0 5px;

            border: 2px solid var(--portal-sidebar);
        }


        /*
        |--------------------------------------------------------------------------
        | Notification Dropdown
        |--------------------------------------------------------------------------
        */

        .portal-notification-menu {
            width: 370px;

            max-width: calc(100vw - 30px);

            padding: 0;

            overflow: hidden;

            border: 1px solid var(--portal-border);

            border-radius: 14px;

            background: var(--portal-card-bg);

            box-shadow:
                0 12px 35px rgba(15, 23, 42, .12);
        }


        .portal-notification-header {
            padding: 16px 18px;

            border-bottom: 1px solid var(--portal-border);

            background: var(--portal-card-bg);
        }


        .portal-notification-item {
            display: flex;

            align-items: flex-start;

            gap: 12px;

            padding: 14px 18px;

            text-decoration: none;

            color: inherit;

            border-bottom: 1px solid var(--portal-border);

            transition: .15s ease;
        }


        .portal-notification-item:hover {
            background: var(--portal-hover);

            color: inherit;
        }


        .portal-notification-item.unread {
            background: rgba(13, 110, 253, .06);
        }


        .portal-notification-icon {
            width: 38px;
            height: 38px;

            flex: 0 0 38px;

            border-radius: 10px;

            display: flex;

            align-items: center;
            justify-content: center;

            color: #ffffff;
        }


        .portal-notification-title {
            font-size: 13px;

            font-weight: 600;

            color: var(--portal-text);
        }


        .portal-notification-message {
            font-size: 12px;

            color: var(--portal-muted);

            line-height: 1.4;

            margin-top: 2px;
        }


        .portal-notification-time {
            font-size: 11px;

            color: #94a3b8;

            margin-top: 4px;
        }


        .portal-notification-footer {
            padding: 12px 18px;

            background: var(--portal-card-bg);
        }


        /*
        |--------------------------------------------------------------------------
        | User
        |--------------------------------------------------------------------------
        */

        .portal-user-button {
            border: 0;

            background: transparent;

            padding: 4px 6px;

            color: var(--portal-text);

            transition: .15s ease;
        }


        .portal-user-button:hover {
            background: var(--portal-hover);

            border-radius: 12px;
        }


        .portal-user-button:focus {
            outline: none;

            box-shadow:
                0 0 0 .2rem rgba(13, 110, 253, .08);

            border-radius: 12px;
        }


        .profile-avatar {
            width: 42px;
            height: 42px;

            border-radius: 50%;

            background: rgba(13, 110, 253, .10);

            color: var(--portal-primary);

            display: flex;

            align-items: center;
            justify-content: center;

            font-weight: 700;

            overflow: hidden;

            flex-shrink: 0;
        }


        .profile-avatar img {
            width: 100%;
            height: 100%;

            object-fit: cover;

            display: block;
        }


        /*
        |--------------------------------------------------------------------------
        | Content
        |--------------------------------------------------------------------------
        */

        .portal-content {
            padding: 30px;
        }


        .portal-card {
            border: 0;

            border-radius: 14px;

            background: var(--portal-card-bg);

            color: var(--portal-text);

            box-shadow:
                0 2px 12px rgba(15, 23, 42, 0.05);

            transition:
                background-color .2s ease,
                color .2s ease;
        }


        /*
        |--------------------------------------------------------------------------
        | Bootstrap / Dark Mode Compatibility
        |--------------------------------------------------------------------------
        */

        html.portal-dark .text-dark,
        html.portal-dark-loading .text-dark {
            color: #f8fafc !important;
        }


        html.portal-dark .text-muted,
        html.portal-dark-loading .text-muted {
            color: #94a3b8 !important;
        }


        html.portal-dark .bg-white,
        html.portal-dark-loading .bg-white {
            background-color: #111827 !important;
        }


        html.portal-dark .bg-light,
        html.portal-dark-loading .bg-light {
            background-color: #1e293b !important;
        }


        html.portal-dark .dropdown-menu,
        html.portal-dark-loading .dropdown-menu {
            background-color: #111827;

            border-color: #263244;

            color: #e5e7eb;
        }


        html.portal-dark .dropdown-item,
        html.portal-dark-loading .dropdown-item {
            color: #e5e7eb;
        }


        html.portal-dark .dropdown-item:hover,
        html.portal-dark-loading .dropdown-item:hover {
            background-color: #1e293b;

            color: #ffffff;
        }


        html.portal-dark .dropdown-divider,
        html.portal-dark-loading .dropdown-divider {
            border-color: #263244;
        }


        html.portal-dark .form-control,
        html.portal-dark .form-select,
        html.portal-dark-loading .form-control,
        html.portal-dark-loading .form-select {
            background-color: var(--portal-input-bg);

            color: #e5e7eb;

            border-color: var(--portal-border);
        }


        html.portal-dark .form-control::placeholder,
        html.portal-dark-loading .form-control::placeholder {
            color: #64748b;
        }


        html.portal-dark .form-control:focus,
        html.portal-dark .form-select:focus,
        html.portal-dark-loading .form-control:focus,
        html.portal-dark-loading .form-select:focus {
            background-color: var(--portal-input-bg);

            color: #ffffff;
        }


        html.portal-dark .table,
        html.portal-dark-loading .table {
            --bs-table-color: #e5e7eb;
            --bs-table-bg: transparent;
        }


        html.portal-dark .table-light,
        html.portal-dark-loading .table-light {
            --bs-table-bg: #1e293b;
            --bs-table-color: #e5e7eb;
        }


        html.portal-dark .border,
        html.portal-dark-loading .border {
            border-color: var(--portal-border) !important;
        }


        /*
        |--------------------------------------------------------------------------
        | Bootstrap Alert Improvements
        |--------------------------------------------------------------------------
        */

        .portal-content .alert {
            border-radius: 12px;
        }


        /*
        |--------------------------------------------------------------------------
        | Dropdown Improvements
        |--------------------------------------------------------------------------
        */

        .dropdown-menu {
            border-color: var(--portal-border);

            border-radius: 12px;

            background: var(--portal-card-bg);
        }


        /*
        |--------------------------------------------------------------------------
        | Scrollbar
        |--------------------------------------------------------------------------
        */

        .portal-sidebar::-webkit-scrollbar {
            width: 5px;
        }


        .portal-sidebar::-webkit-scrollbar-track {
            background: transparent;
        }


        .portal-sidebar::-webkit-scrollbar-thumb {
            background: #cbd5e1;

            border-radius: 10px;
        }


        html.portal-dark .portal-sidebar::-webkit-scrollbar-thumb,
        html.portal-dark-loading .portal-sidebar::-webkit-scrollbar-thumb {
            background: #475569;
        }


        /*
        |--------------------------------------------------------------------------
        | Responsive
        |--------------------------------------------------------------------------
        */

        @media (max-width: 991px) {

            .portal-sidebar {
                width: 220px;
            }


            .portal-main {
                margin-left: 220px;
            }


            .portal-topbar {
                padding: 0 20px;
            }


            .portal-content {
                padding: 25px 20px;
            }

        }


        @media (max-width: 767px) {

            .portal-sidebar {
                position: static;

                width: 100%;

                min-height: auto;

                border-right: 0;

                border-bottom: 1px solid var(--portal-border);
            }


            .portal-main {
                margin-left: 0;
            }


            .portal-topbar {
                position: static;

                padding: 0 15px;
            }


            .portal-content {
                padding: 20px 15px;
            }


            .portal-notification-menu {
                width: 340px;
            }


            .portal-brand {
                padding: 0 18px;
            }


            .portal-nav {
                padding: 15px 12px;
            }

        }


        @media (max-width: 575px) {

            .portal-page-title {
                font-size: 16px;
            }


            .portal-notification-menu {
                position: fixed !important;

                top: 72px !important;

                right: 15px !important;

                left: 15px !important;

                width: auto;

                max-width: none;

                transform: none !important;
            }


            .portal-user-button .bi-chevron-down {
                display: none;
            }


            .portal-topbar > div:last-child {
                gap: .4rem !important;
            }


            .portal-icon-button,
            .portal-notification-button {
                width: 38px;
                height: 38px;
            }

        }

    </style>

</head>


<body>

<div class="portal-wrapper">


    {{-- ================================================================== --}}
    {{-- SIDEBAR --}}
    {{-- ================================================================== --}}

    <aside class="portal-sidebar">


        {{-- ================================================================== --}}
        {{-- BRAND --}}
        {{-- ================================================================== --}}

        <div class="portal-brand">

            <a
                href="{{ route('portal') }}"
                class="portal-brand-link"
            >

                <span class="portal-brand-icon">

                    <i class="bi bi-buildings"></i>

                </span>


                <span class="portal-brand-text">

                    <strong>
                        ImmoFlow
                    </strong>

                </span>

            </a>

        </div>


        {{-- ================================================================== --}}
        {{-- NAVIGATION --}}
        {{-- ================================================================== --}}

        <nav class="portal-nav">


            {{-- Main --}}
            <div class="portal-nav-label">
                Main
            </div>


            {{-- Dashboard --}}
            <a
                href="{{ route('portal') }}"
                class="nav-link {{ request()->routeIs('portal') ? 'active' : '' }}"
            >

                <i class="bi bi-grid"></i>

                <span>
                    Dashboard
                </span>

            </a>


            {{-- Tenancy --}}
            <a
                href="{{ route('portal.tenancy') }}"
                class="nav-link {{ request()->routeIs('portal.tenancy*') ? 'active' : '' }}"
            >

                <i class="bi bi-house-door"></i>

                <span>
                    My Tenancy
                </span>

            </a>


            {{-- Maintenance --}}
            <a
                href="{{ route('portal.maintenance') }}"
                class="nav-link {{ request()->routeIs('portal.maintenance*') ? 'active' : '' }}"
            >

                <i class="bi bi-tools"></i>

                <span>
                    Maintenance
                </span>

            </a>


            {{-- Payments --}}
            <a
                href="{{ route('portal.payments') }}"
                class="nav-link {{ request()->routeIs('portal.payments*') ? 'active' : '' }}"
            >

                <i class="bi bi-credit-card"></i>

                <span>
                    Payments
                </span>

            </a>


            {{-- Account --}}
            <div class="portal-nav-label mt-4">
                Account
            </div>


            {{-- Profile --}}
            <a
                href="{{ route('portal.profile') }}"
                class="nav-link {{ request()->routeIs('portal.profile*') ? 'active' : '' }}"
            >

                <i class="bi bi-person"></i>

                <span>
                    My Profile
                </span>

            </a>


            {{-- Password --}}
            <a
                href="{{ route('portal.password') }}"
                class="nav-link {{ request()->routeIs('portal.password*') ? 'active' : '' }}"
            >

                <i class="bi bi-shield-lock"></i>

                <span>
                    Change Password
                </span>

            </a>


            {{-- System --}}
            <div class="portal-nav-label mt-4">
                System
            </div>


            {{-- Notifications --}}
            @php
                $sidebarUnreadCount = auth()
                    ->user()
                    ->unreadNotifications()
                    ->count();
            @endphp


            <a
                href="{{ route('portal.notifications') }}"
                class="nav-link {{ request()->routeIs('portal.notifications*') ? 'active' : '' }}"
            >

                <i class="bi bi-bell"></i>

                <span>
                    Notifications
                </span>


                @if($sidebarUnreadCount > 0)

                    <span class="badge bg-danger ms-auto">

                        {{ $sidebarUnreadCount > 99
                            ? '99+'
                            : $sidebarUnreadCount }}

                    </span>

                @endif

            </a>


            {{-- Logout --}}
            <form
                method="POST"
                action="{{ route('logout') }}"
                class="mt-3"
            >

                @csrf

                <button
                    type="submit"
                    class="nav-link portal-logout border-0 bg-transparent w-100 text-start"
                >

                    <i class="bi bi-box-arrow-right"></i>

                    <span>
                        Logout
                    </span>

                </button>

            </form>

        </nav>

    </aside>


    {{-- ================================================================== --}}
    {{-- MAIN --}}
    {{-- ================================================================== --}}

    <main class="portal-main">


        {{-- ================================================================== --}}
        {{-- TOPBAR --}}
        {{-- ================================================================== --}}

        <header class="portal-topbar">


            {{-- Page title --}}
            <div>

                <h1 class="portal-page-title">

                    @yield(
                        'page-heading',
                        'Tenant Portal'
                    )

                </h1>

            </div>


            {{-- Right side --}}
            <div class="d-flex align-items-center gap-3">


                {{-- ========================================================== --}}
                {{-- DARK / LIGHT MODE --}}
                {{-- ========================================================== --}}

                <button
                    type="button"
                    id="appearanceToggle"
                    class="portal-icon-button"
                    title="{{ auth()->user()->appearance === 'dark'
                        ? 'Switch to light mode'
                        : 'Switch to dark mode' }}"
                    aria-label="Toggle dark/light mode"
                >

                    <i
                        id="appearanceIcon"
                        class="bi {{ auth()->user()->appearance === 'dark'
                            ? 'bi-sun'
                            : 'bi-moon-stars' }}"
                    ></i>

                </button>


                {{-- ========================================================== --}}
                {{-- NOTIFICATION BELL --}}
                {{-- ========================================================== --}}

                <div class="dropdown">


                    <button
                        class="portal-notification-button"
                        type="button"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="outside"
                        aria-expanded="false"
                        title="Notifications"
                    >

                        <i class="bi bi-bell"></i>


                        @php
                            $unreadCount = auth()
                                ->user()
                                ->unreadNotifications()
                                ->count();
                        @endphp


                        @if($unreadCount > 0)

                            <span class="portal-notification-badge">

                                {{ $unreadCount > 99
                                    ? '99+'
                                    : $unreadCount }}

                            </span>

                        @endif

                    </button>


                    {{-- Notification dropdown --}}
                    <div class="dropdown-menu dropdown-menu-end portal-notification-menu">


                        {{-- Header --}}
                        <div class="portal-notification-header">

                            <div class="d-flex justify-content-between align-items-center">

                                <div>

                                    <div class="fw-bold">
                                        Notifications
                                    </div>

                                    <small class="text-muted">

                                        {{ $unreadCount }}

                                        {{ $unreadCount === 1
                                            ? 'unread notification'
                                            : 'unread notifications' }}

                                    </small>

                                </div>


                                @if($unreadCount > 0)

                                    <form
                                        method="POST"
                                        action="{{ route('portal.notifications.mark-all-as-read') }}"
                                    >

                                        @csrf

                                        <button
                                            type="submit"
                                            class="btn btn-link btn-sm text-decoration-none p-0"
                                        >

                                            Mark all read

                                        </button>

                                    </form>

                                @endif

                            </div>

                        </div>


                        {{-- Recent notifications --}}
                        @php
                            $recentNotifications = auth()
                                ->user()
                                ->notifications()
                                ->latest()
                                ->take(5)
                                ->get();
                        @endphp


                        @forelse($recentNotifications as $notification)

                            @php

                                $notificationIcon =
                                    $notification->data['icon']
                                    ?? 'bi-bell';


                                $notificationColor =
                                    $notification->data['badge_color']
                                    ?? 'bg-primary';

                            @endphp


                            <a
                                href="{{ route(
                                    'portal.notifications.read',
                                    $notification->id
                                ) }}"
                                class="portal-notification-item
                                    {{ $notification->unread()
                                        ? 'unread'
                                        : '' }}"
                            >


                                {{-- Icon --}}
                                <div
                                    class="portal-notification-icon
                                        {{ $notificationColor }}"
                                >

                                    <i
                                        class="bi {{ $notificationIcon }}"
                                    ></i>

                                </div>


                                {{-- Content --}}
                                <div class="flex-grow-1">


                                    <div class="portal-notification-title">

                                        {{ $notification->data['title']
                                            ?? 'Notification' }}

                                    </div>


                                    <div class="portal-notification-message">

                                        {{ $notification->data['message']
                                            ?? '' }}

                                    </div>


                                    <div class="portal-notification-time">

                                        {{ $notification->created_at->diffForHumans() }}

                                    </div>

                                </div>


                                {{-- Unread indicator --}}
                                @if($notification->unread())

                                    <span class="text-primary">

                                        <i class="bi bi-circle-fill small"></i>

                                    </span>

                                @endif


                            </a>

                        @empty


                            <div class="text-center text-muted py-4">

                                <i
                                    class="bi bi-bell-slash fs-3 d-block mb-2"
                                ></i>

                                <div>
                                    No notifications
                                </div>

                            </div>


                        @endforelse


                        {{-- Footer --}}
                        <div class="portal-notification-footer">

                            <a
                                href="{{ route('portal.notifications') }}"
                                class="btn btn-light btn-sm w-100"
                            >

                                <i class="bi bi-list-ul me-1"></i>

                                View All Notifications

                            </a>

                        </div>

                    </div>

                </div>


                {{-- ========================================================== --}}
                {{-- USER MENU --}}
                {{-- ========================================================== --}}

                <div class="dropdown">


                    <button
                        class="portal-user-button d-flex align-items-center gap-2"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >


                        {{-- Avatar --}}
                        <div class="profile-avatar">

                            @if(auth()->user()->profile_photo)

                                <img
                                    src="{{ asset(
                                        'storage/' .
                                        auth()->user()->profile_photo
                                    ) }}"
                                    alt="{{ auth()->user()->name }}"
                                >

                            @else

                                {{ strtoupper(
                                    substr(
                                        auth()->user()->name,
                                        0,
                                        1
                                    )
                                ) }}

                            @endif

                        </div>


                        {{-- Name --}}
                        <div class="text-start d-none d-md-block">

                            <div class="fw-semibold small">

                                {{ auth()->user()->name }}

                            </div>


                            <div class="text-muted small">

                                Tenant

                            </div>

                        </div>


                        <i class="bi bi-chevron-down text-muted"></i>

                    </button>


                    {{-- User dropdown --}}
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">


                        <li>

                            <a
                                class="dropdown-item"
                                href="{{ route('portal.profile') }}"
                            >

                                <i class="bi bi-person me-2"></i>

                                My Profile

                            </a>

                        </li>


                        <li>

                            <a
                                class="dropdown-item"
                                href="{{ route('portal.password') }}"
                            >

                                <i class="bi bi-shield-lock me-2"></i>

                                Change Password

                            </a>

                        </li>


                        <li>

                            <a
                                class="dropdown-item"
                                href="{{ route('portal.notifications') }}"
                            >

                                <i class="bi bi-bell me-2"></i>

                                Notifications

                            </a>

                        </li>


                        <li>

                            <hr class="dropdown-divider">

                        </li>


                        <li>

                            <form
                                method="POST"
                                action="{{ route('logout') }}"
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="dropdown-item"
                                >

                                    <i class="bi bi-box-arrow-right me-2"></i>

                                    Logout

                                </button>

                            </form>

                        </li>

                    </ul>

                </div>

            </div>

        </header>


        {{-- ================================================================== --}}
        {{-- CONTENT --}}
        {{-- ================================================================== --}}

        <div class="portal-content">


            {{-- Success --}}
            @if(session('success'))

                <div class="alert alert-success alert-dismissible fade show">

                    <i class="bi bi-check-circle me-2"></i>

                    {{ session('success') }}


                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                    ></button>

                </div>

            @endif


            {{-- Error --}}
            @if(session('error'))

                <div class="alert alert-danger alert-dismissible fade show">

                    <i class="bi bi-exclamation-circle me-2"></i>

                    {{ session('error') }}


                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                    ></button>

                </div>

            @endif


            {{-- Validation Errors --}}
            @if($errors->any())

                <div class="alert alert-danger">

                    <div class="fw-semibold mb-2">

                        Please correct the following:

                    </div>


                    <ul class="mb-0">

                        @foreach($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- Page Content --}}
            @yield('content')

        </div>

    </main>

</div>


{{-- Bootstrap JS --}}
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


{{-- ====================================================================== --}}
{{-- PORTAL APPEARANCE SCRIPT --}}
{{-- ====================================================================== --}}

<script>

    document.addEventListener('DOMContentLoaded', function () {

        const toggle = document.getElementById('appearanceToggle');

        const icon = document.getElementById('appearanceIcon');

        if (!toggle || !icon) {
            return;
        }


        const html = document.documentElement;

        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute('content');


        function applyAppearance(mode) {

            if (mode === 'dark') {

                html.classList.add('portal-dark');

                html.classList.remove(
                    'portal-dark-loading'
                );

                icon.classList.remove(
                    'bi-moon-stars'
                );

                icon.classList.add(
                    'bi-sun'
                );

                toggle.setAttribute(
                    'title',
                    'Switch to light mode'
                );

            } else {

                html.classList.remove(
                    'portal-dark'
                );

                html.classList.remove(
                    'portal-dark-loading'
                );

                icon.classList.remove(
                    'bi-sun'
                );

                icon.classList.add(
                    'bi-moon-stars'
                );

                toggle.setAttribute(
                    'title',
                    'Switch to dark mode'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Initial Appearance
        |--------------------------------------------------------------------------
        */

        const initialAppearance =
            @json(auth()->user()->appearance ?? 'light');


        if (initialAppearance === 'dark') {

            html.classList.add('portal-dark');

        }

        html.classList.remove(
            'portal-dark-loading'
        );


        /*
        |--------------------------------------------------------------------------
        | Toggle Appearance
        |--------------------------------------------------------------------------
        */

        toggle.addEventListener('click', async function () {

            const currentlyDark =
                html.classList.contains('portal-dark');

            const newMode =
                currentlyDark
                    ? 'light'
                    : 'dark';


            /*
            |--------------------------------------------------------------------------
            | Update UI immediately
            |--------------------------------------------------------------------------
            */

            applyAppearance(newMode);


            toggle.disabled = true;


            try {

                const response = await fetch(
                    "{{ route('portal.appearance') }}",
                    {
                        method: 'POST',

                        headers: {
                            'Content-Type': 'application/json',

                            'Accept': 'application/json',

                            'X-CSRF-TOKEN':
                                csrfToken
                        },

                        body: JSON.stringify({
                            appearance: newMode
                        })
                    }
                );


                if (!response.ok) {

                    throw new Error(
                        'Unable to save appearance preference.'
                    );

                }

            } catch (error) {

                /*
                |--------------------------------------------------------------------------
                | Revert UI if save fails
                |--------------------------------------------------------------------------
                */

                applyAppearance(
                    currentlyDark
                        ? 'dark'
                        : 'light'
                );

                console.error(error);

                alert(
                    'Your appearance preference could not be saved. Please try again.'
                );

            } finally {

                toggle.disabled = false;

            }

        });

    });

</script>


@stack('scripts')

</body>

</html>