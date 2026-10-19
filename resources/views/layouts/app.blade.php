{{-- resources/views/layouts/app.blade.php --}}

<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    data-theme="{{ auth()->user()?->appearance ?? 'light' }}"
    data-appearance="{{ auth()->user()?->appearance ?? 'light' }}"
>

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
        @yield('title', 'Dashboard') | ImmoFlow
    </title>

    <script>
        (() => {
            const root = document.documentElement;
            const savedTheme = localStorage.getItem('app_theme');
            if (savedTheme) {
                root.dataset.theme = savedTheme;
                root.dataset.appearance = savedTheme;
            } else if (root.dataset.appearance === 'system') {
                const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
                root.dataset.theme = mediaQuery.matches ? 'dark' : 'light';
            }
        })();

        function applyAppTheme(theme) {
            const root = document.documentElement;
            root.dataset.theme = theme;
            root.dataset.appearance = theme;
            localStorage.setItem('app_theme', theme);
            const icon = document.getElementById('theme-toggle-icon');
            if (icon) {
                if (theme === 'dark') {
                    icon.className = 'bi bi-sun-fill text-warning';
                } else {
                    icon.className = 'bi bi-moon-stars';
                }
            }
        }

        function toggleAppTheme() {
            const current = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
            applyAppTheme(current);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const current = document.documentElement.dataset.theme || 'light';
            applyAppTheme(current);
        });
    </script>


    {{-- =========================================================
         BOOTSTRAP
         ========================================================= --}}

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    {{-- =========================================================
         BOOTSTRAP ICONS
         ========================================================= --}}

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >


    {{-- =========================================================
         APPLICATION STYLES
         ========================================================= --}}

    <style>

        :root {

            --pm-navy: #16233A;
            --pm-navy-dark: #0F1A2B;

            --pm-teal: #0D9488;
            --pm-teal-dark: #0F766E;

            --pm-blue: #2563EB;
            --pm-green: #16A34A;
            --pm-amber: #D97706;
            --pm-red: #DC3545;

            --pm-bg: #F6F7F9;
            --pm-white: #FFFFFF;

            --pm-border: #E6E9EE;

            --pm-text: #16233A;
            --pm-muted: #6B7280;

            --sidebar-width: 260px;
            --sidebar-collapsed-width: 78px;

            --topbar-height: 70px;

            --pm-radius: 14px;

            --pm-shadow:
                0 4px 18px rgba(22, 35, 58, .05);
        }

        html[data-theme="dark"] {
            color-scheme: dark;
            --pm-navy: #F1F5F9;
            --pm-navy-dark: #FFFFFF;
            --pm-teal: #2DD4BF;
            --pm-teal-dark: #5EEAD4;
            --pm-blue: #60A5FA;
            --pm-green: #4ADE80;
            --pm-amber: #FBBF24;
            --pm-red: #FB7185;
            --pm-bg: #0B1220;
            --pm-white: #141E2E;
            --pm-border: #29384F;
            --pm-text: #E7EDF7;
            --pm-muted: #A9B6C9;
            --pm-shadow: 0 16px 36px rgba(0, 0, 0, .22);
        }

        @media (prefers-color-scheme: dark) {
            html[data-theme="system"] {
                color-scheme: dark;
                --pm-navy: #F1F5F9;
                --pm-navy-dark: #FFFFFF;
                --pm-teal: #2DD4BF;
                --pm-teal-dark: #5EEAD4;
                --pm-blue: #60A5FA;
                --pm-green: #4ADE80;
                --pm-amber: #FBBF24;
                --pm-red: #FB7185;
                --pm-bg: #0B1220;
                --pm-white: #141E2E;
                --pm-border: #29384F;
                --pm-text: #E7EDF7;
                --pm-muted: #A9B6C9;
                --pm-shadow: 0 16px 36px rgba(0, 0, 0, .22);
            }
        }

        html[data-theme="dark"] .sidebar,
        html[data-theme="dark"] .topbar,
        html[data-theme="dark"] .app-footer,
        html[data-theme="dark"] .pm-card,
        html[data-theme="dark"] .settings-card,
        html[data-theme="dark"] .settings-menu,
        html[data-theme="dark"] .form-control,
        html[data-theme="dark"] .form-select,
        html[data-theme="dark"] .table,
        html[data-theme="dark"] .dropdown-menu,
        html[data-theme="dark"] .modal-content,
        html[data-theme="dark"] .card,
        html[data-theme="dark"] .hero-stat,
        html[data-theme="dark"] .fin-stat-card,
        html[data-theme="dark"] .dashboard-card,
        html[data-theme="dark"] .units-card,
        html[data-theme="dark"] .units-summary-card,
        html[data-theme="dark"] .us-card,
        html[data-theme="dark"] .us-summary,
        html[data-theme="dark"] .ue-card,
        html[data-theme="dark"] .ue-side-card,
        html[data-theme="dark"] .quick-action,
        html[data-theme="dark"] .list-group-item {
            background-color: var(--pm-white) !important;
            color: var(--pm-text) !important;
            border-color: var(--pm-border) !important;
        }

        html[data-theme="dark"] .table td,
        html[data-theme="dark"] .table th,
        html[data-theme="dark"] .text-muted,
        html[data-theme="dark"] .text-secondary,
        html[data-theme="dark"] .settings-card-description,
        html[data-theme="dark"] .settings-help,
        html[data-theme="dark"] .dashboard-welcome p,
        html[data-theme="dark"] .hero-stat-label,
        html[data-theme="dark"] .fin-stat-label,
        html[data-theme="dark"] .hero-stat-meta,
        html[data-theme="dark"] .fin-stat-meta,
        html[data-theme="dark"] .dashboard-card-subtitle,
        html[data-theme="dark"] .finance-item-label,
        html[data-theme="dark"] .quick-action-description,
        html[data-theme="dark"] .attention-description,
        html[data-theme="dark"] .occupancy-description {
            color: var(--pm-muted) !important;
            border-color: var(--pm-border) !important;
        }

        html[data-theme="dark"] h1,
        html[data-theme="dark"] h2,
        html[data-theme="dark"] h3,
        html[data-theme="dark"] h4,
        html[data-theme="dark"] h5,
        html[data-theme="dark"] h6,
        html[data-theme="dark"] .text-dark,
        html[data-theme="dark"] .dashboard-welcome h1,
        html[data-theme="dark"] .hero-stat-value,
        html[data-theme="dark"] .fin-stat-value,
        html[data-theme="dark"] .dashboard-card-title,
        html[data-theme="dark"] .occupancy-title,
        html[data-theme="dark"] .quick-action-title,
        html[data-theme="dark"] .attention-title {
            color: #F8FAFC !important;
        }

        html[data-theme="dark"] .finance-item,
        html[data-theme="dark"] .currency-pills,
        html[data-theme="dark"] .table-light,
        html[data-theme="dark"] .dashboard-card-header,
        html[data-theme="dark"] .modal-header,
        html[data-theme="dark"] .modal-footer {
            background-color: #0E1726 !important;
            border-color: #29384F !important;
        }

        html[data-theme="dark"] .currency-pill {
            color: #93A4BA !important;
        }

        html[data-theme="dark"] .currency-pill.active {
            background-color: #1E2C42 !important;
            color: #2DD4BF !important;
        }

        html[data-theme="dark"] .topbar {
            background: rgba(20, 30, 46, .92) !important;
        }

        /* Refined dark-mode surfaces and interaction states. */
        html[data-theme="dark"] .sidebar-brand,
        html[data-theme="dark"] .sidebar-footer {
            background: #101A29 !important;
        }

        html[data-theme="dark"] .sidebar-brand-icon,
        html[data-theme="dark"] .sidebar-avatar,
        html[data-theme="dark"] .topbar-avatar {
            background: linear-gradient(135deg, #14B8A6 0%, #0F766E 100%) !important;
            box-shadow: 0 6px 16px rgba(20, 184, 166, .22);
        }

        html[data-theme="dark"] .sidebar-brand-icon,
        html[data-theme="dark"] .sidebar-brand-icon i,
        html[data-theme="dark"] .sidebar-avatar,
        html[data-theme="dark"] .topbar-avatar {
            color: #FFFFFF !important;
        }

        html[data-theme="dark"] .sidebar-brand-text strong,
        html[data-theme="dark"] .sidebar-brand-link,
        html[data-theme="dark"] .topbar-heading h1,
        html[data-theme="dark"] .topbar-user-name {
            color: #F8FAFC !important;
        }

        html[data-theme="dark"] .sidebar,
        html[data-theme="dark"] .settings-menu {
            background: #101A29 !important;
        }

        html[data-theme="dark"] .pm-card,
        html[data-theme="dark"] .settings-card {
            background: linear-gradient(145deg, #172338 0%, #141E2E 100%) !important;
            box-shadow: 0 12px 28px rgba(0, 0, 0, .18) !important;
        }

        html[data-theme="dark"] .sidebar .nav-link,
        html[data-theme="dark"] .sidebar-user,
        html[data-theme="dark"] .topbar-user,
        html[data-theme="dark"] .settings-menu-link,
        html[data-theme="dark"] .dropdown-item {
            color: #C4D0E0 !important;
        }

        html[data-theme="dark"] .sidebar-section-label,
        html[data-theme="dark"] .sidebar-brand-text small,
        html[data-theme="dark"] .sidebar-user-role,
        html[data-theme="dark"] .topbar-user-role,
        html[data-theme="dark"] .settings-menu-title {
            color: #7F90A8 !important;
        }

        html[data-theme="dark"] .sidebar .nav-link:hover,
        html[data-theme="dark"] .sidebar-user:hover,
        html[data-theme="dark"] .topbar-user:hover,
        html[data-theme="dark"] .settings-menu-link:hover,
        html[data-theme="dark"] .dropdown-item:hover,
        html[data-theme="dark"] .attention-link:hover,
        html[data-theme="dark"] .quick-action:hover {
            background: #202E45 !important;
            color: #F1F5F9 !important;
        }

        html[data-theme="dark"] .sidebar .nav-link.active,
        html[data-theme="dark"] .settings-menu-link.active {
            background: rgba(45, 212, 191, .14) !important;
            color: #5EEAD4 !important;
        }

        html[data-theme="dark"] .form-control,
        html[data-theme="dark"] .form-select {
            background-color: #0E1726 !important;
            color: #E7EDF7 !important;
            border-color: #29384F !important;
        }

        html[data-theme="dark"] .form-control::placeholder {
            color: #71829A !important;
        }

        html[data-theme="dark"] .form-control:focus,
        html[data-theme="dark"] .form-select:focus {
            border-color: #2DD4BF !important;
            box-shadow: 0 0 0 .2rem rgba(45, 212, 191, .14) !important;
        }

        html[data-theme="dark"] .table > :not(caption) > * > * {
            background-color: transparent !important;
            color: #D8E1EE !important;
            border-bottom-color: #29384F !important;
        }

        html[data-theme="dark"] .table thead th {
            color: #93A4BA !important;
            background: #101A29 !important;
            border-bottom-color: #29384F !important;
        }

        html[data-theme="dark"] .table tbody tr:hover > * {
            background-color: rgba(45, 212, 191, .045) !important;
        }

        html[data-theme="dark"] .pagination .page-link {
            background-color: #141E2E !important;
            border-color: #29384F !important;
            color: #C4D0E0 !important;
        }

        html[data-theme="dark"] .pagination .page-item.active .page-link {
            background-color: #0D9488 !important;
            border-color: #0D9488 !important;
            color: #FFFFFF !important;
        }

        html[data-theme="dark"] .badge.bg-light,
        html[data-theme="dark"] .badge.bg-secondary {
            background-color: #1E2C42 !important;
            color: #D8E1EE !important;
        }

        html[data-theme="dark"] .pm-btn-outline,
        html[data-theme="dark"] .btn-light {
            background: #1A2940 !important;
            color: #E7EDF7 !important;
            border-color: #354763 !important;
        }

        html[data-theme="dark"] .pm-btn-outline:hover,
        html[data-theme="dark"] .btn-light:hover {
            background: #253650 !important;
            border-color: #4A607F !important;
        }

        html[data-theme="dark"] .btn-close {
            filter: invert(1) grayscale(1) brightness(2);
        }


        /* =====================================================
           GLOBAL
           ===================================================== */

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html {
            min-height: 100%;
            scroll-behavior: smooth;
        }

        body {

            min-height: 100vh;

            margin: 0;

            background: var(--pm-bg);

            color: var(--pm-text);

            font-family:
                Inter,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;

            font-size: .9rem;
        }

        a {
            text-decoration: none;
        }

        button,
        a {
            -webkit-tap-highlight-color: transparent;
        }

        button:focus-visible,
        a:focus-visible {

            outline: 2px solid var(--pm-teal);

            outline-offset: 2px;
        }


        /* =====================================================
           SIDEBAR
           ===================================================== */

        .sidebar {

            position: fixed;

            top: 0;
            left: 0;
            bottom: 0;

            width: var(--sidebar-width);

            display: flex;
            flex-direction: column;

            background: #FFFFFF;

            border-right: 1px solid var(--pm-border);

            z-index: 1040;

            overflow: hidden;

            transition:
                width .2s ease,
                transform .25s ease;
        }


        /* =====================================================
           BRAND
           ===================================================== */

        .sidebar-brand {

            height: var(--topbar-height);

            display: flex;
            align-items: center;

            padding: 0 1.1rem;

            flex-shrink: 0;

            background: #FFFFFF;

            border-bottom: 1px solid var(--pm-border);
        }

        .sidebar-brand-link {

            width: 100%;

            display: flex;
            align-items: center;

            gap: .75rem;

            color: var(--pm-navy);
        }

        .sidebar-brand-icon {

            width: 40px;
            height: 40px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            flex-shrink: 0;

            border-radius: 11px;

            background: var(--pm-navy);

            color: #FFFFFF;

            font-size: 1.15rem;
        }

        .sidebar-brand-text {

            display: flex;
            flex-direction: column;

            line-height: 1.15;

            white-space: nowrap;
        }

        .sidebar-brand-text strong {

            font-size: .95rem;

            font-weight: 700;
        }

        .sidebar-brand-text small {

            margin-top: .2rem;

            color: #8A93A3;

            font-size: .67rem;
        }


        /* =====================================================
           SIDEBAR NAVIGATION
           ===================================================== */

        .sidebar-nav {

            flex: 1;

            min-height: 0;

            overflow-y: auto;
            overflow-x: hidden;

            padding: .8rem .7rem 1rem;

            -webkit-overflow-scrolling: touch;

            overscroll-behavior: contain;

            scrollbar-width: thin;

            scrollbar-color:
                #D9DEE6
                transparent;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {

            background: #D9DEE6;

            border-radius: 99px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: #BEC5D0;
        }

        .sidebar-section {

            margin-bottom: 1.15rem;
        }

        .sidebar-section-label {

            padding:
                .4rem
                .7rem
                .5rem;

            color: #9AA2AF;

            font-size: .64rem;

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: .08em;

            white-space: nowrap;
        }


        /* =====================================================
           NAV LINKS
           ===================================================== */

        .sidebar .nav-link {

            position: relative;

            display: flex;
            align-items: center;

            width: 100%;

            min-height: 42px;

            margin-bottom: .18rem;

            padding: .65rem .75rem;

            border-radius: 9px;

            color: #5F6979;

            font-size: .85rem;

            font-weight: 500;

            white-space: nowrap;

            transition:
                background-color .15s ease,
                color .15s ease;
        }

        .sidebar .nav-link i {

            width: 21px;

            margin-right: .7rem;

            flex-shrink: 0;

            text-align: center;

            font-size: 1rem;
        }

        .sidebar .nav-link:hover {

            background: #F3F6F8;

            color: var(--pm-navy);
        }

        .sidebar .nav-link.active {

            background: rgba(13, 148, 136, .10);

            color: var(--pm-teal);

            font-weight: 600;
        }

        .sidebar .nav-link.active::before {

            content: "";

            position: absolute;

            top: 8px;
            bottom: 8px;
            left: 0;

            width: 3px;

            border-radius:
                0
                4px
                4px
                0;

            background: var(--pm-teal);
        }


        /* =====================================================
           SIDEBAR FOOTER
           ===================================================== */

        .sidebar-footer {

            flex-shrink: 0;

            padding: .7rem;

            background: #FFFFFF;

            border-top: 1px solid var(--pm-border);
        }

        .sidebar-divider {

            height: 1px;

            margin:
                .5rem
                .35rem;

            background: var(--pm-border);
        }


        /* =====================================================
           USER
           ===================================================== */

        .sidebar-user {

            display: flex;
            align-items: center;

            gap: .7rem;

            width: 100%;

            padding: .65rem .6rem;

            border-radius: 10px;

            color: var(--pm-navy);
        }

        .sidebar-user:hover,
        .sidebar-user.active {

            background: #F3F6F8;
        }

        .sidebar-avatar {

            width: 36px;
            height: 36px;

            display: flex;
            align-items: center;
            justify-content: center;

            flex-shrink: 0;

            overflow: hidden;

            border-radius: 50%;

            background: var(--pm-navy);

            color: #FFFFFF;

            font-size: .8rem;

            font-weight: 700;
        }

        .sidebar-avatar img {

            width: 100%;
            height: 100%;

            display: block;

            object-fit: cover;

            border-radius: 50%;
        }

        .sidebar-user-info {

            min-width: 0;

            flex: 1;
        }

        .sidebar-user-name {

            overflow: hidden;

            color: var(--pm-navy);

            font-size: .82rem;

            font-weight: 600;

            text-overflow: ellipsis;

            white-space: nowrap;
        }

        .sidebar-user-role {

            margin-top: .1rem;

            color: #8A93A3;

            font-size: .67rem;

            text-transform: capitalize;

            white-space: nowrap;
        }

        .sidebar-user-arrow {

            color: #A5ADBA;

            font-size: .72rem;
        }


        /* =====================================================
           LOGOUT
           ===================================================== */

        .sidebar-logout-form {
            margin-top: .2rem;
        }

        .sidebar-logout {

            border: 0;

            background: transparent;

            cursor: pointer;

            text-align: left;
        }

        .sidebar-logout:hover {

            background:
                rgba(220, 53, 69, .07)
            !important;

            color: var(--pm-red)
            !important;
        }


        /* =====================================================
           MAIN WRAPPER
           ===================================================== */

        .main-wrapper {

            min-height: 100vh;

            margin-left: var(--sidebar-width);

            display: flex;

            flex-direction: column;

            transition:
                margin-left .2s ease;
        }

        body.sidebar-collapsed .main-wrapper {
            margin-left: var(--sidebar-collapsed-width);
        }


        /* =====================================================
           TOPBAR
           ===================================================== */

        .topbar {

            position: sticky;

            top: 0;

            height: var(--topbar-height);

            display: flex;

            align-items: center;

            padding:
                0
                1.5rem;

            background:
                rgba(255, 255, 255, .97);

            border-bottom:
                1px solid
                var(--pm-border);

            backdrop-filter: blur(10px);

            z-index: 1020;
        }

        .topbar-left {

            display: flex;

            align-items: center;

            min-width: 0;

            gap: 1rem;
        }

        .sidebar-toggle,
        .mobile-menu-button {

            width: 38px;
            height: 38px;

            display: inline-flex;

            align-items: center;
            justify-content: center;

            padding: 0;

            border:
                1px solid
                var(--pm-border);

            border-radius: 9px;

            background: #FFFFFF;

            color: var(--pm-navy);

            cursor: pointer;
        }

        .sidebar-toggle:hover,
        .mobile-menu-button:hover {

            background: #F3F6F8;
        }

        .mobile-menu-button {
            display: none;
        }

        .topbar-heading {

            min-width: 0;
        }

        .topbar-heading h1 {

            overflow: hidden;

            margin: 0;

            color: var(--pm-navy);

            font-size: 1.05rem;

            font-weight: 700;

            text-overflow: ellipsis;

            white-space: nowrap;
        }

        .topbar-right {

            display: flex;

            align-items: center;

            gap: .75rem;

            margin-left: auto;
        }


        /* =====================================================
           NOTIFICATIONS
           ===================================================== */

        .notification-button {

            position: relative;

            width: 38px;
            height: 38px;

            display: inline-flex;

            align-items: center;
            justify-content: center;

            padding: 0;

            border:
                1px solid
                var(--pm-border);

            border-radius: 9px;

            background: #FFFFFF;

            color: #667085;

            cursor: pointer;
        }

        .notification-button:hover {

            background: #F3F6F8;

            color: var(--pm-navy);
        }


        /* =====================================================
           TOPBAR USER
           ===================================================== */

        .topbar-user {

            display: flex;

            align-items: center;

            gap: .6rem;

            padding:
                .3rem
                .45rem;

            border:
                1px solid
                transparent;

            border-radius: 10px;

            color: var(--pm-navy);

            background: transparent;

            cursor: pointer;
        }

        .topbar-user:hover {

            background: #F5F7F9;

            border-color: var(--pm-border);
        }

        .topbar-avatar {

            width: 36px;
            height: 36px;

            display: flex;

            align-items: center;
            justify-content: center;

            overflow: hidden;

            border-radius: 50%;

            background: var(--pm-navy);

            color: #FFFFFF;

            font-size: .8rem;

            font-weight: 700;
        }

        .topbar-avatar img {

            width: 100%;
            height: 100%;

            display: block;

            object-fit: cover;

            border-radius: 50%;
        }

        .topbar-user-info {

            display: flex;

            flex-direction: column;

            align-items: flex-start;

            line-height: 1.15;
        }

        .topbar-user-name {

            max-width: 150px;

            overflow: hidden;

            font-size: .78rem;

            font-weight: 600;

            text-overflow: ellipsis;

            white-space: nowrap;
        }

        .topbar-user-role {

            margin-top: .18rem;

            color: #8A93A3;

            font-size: .65rem;

            text-transform: capitalize;
        }


        /* =====================================================
           MAIN CONTENT
           ===================================================== */

        .main-content {

            flex: 1;

            min-width: 0;

            padding: 1.5rem;
        }


        /* =====================================================
           ALERTS
           ===================================================== */

        .pm-alert {

            display: flex;

            align-items: flex-start;

            gap: .75rem;

            margin-bottom: 1rem;

            padding:
                .85rem
                1rem;

            border-radius: 10px;
        }


        /* =====================================================
           GENERAL CARDS
           ===================================================== */

        .pm-card {

            overflow: hidden;

            background: #FFFFFF;

            border:
                1px solid
                var(--pm-border);

            border-radius:
                var(--pm-radius);

            box-shadow:
                var(--pm-shadow);
        }

        .pm-card-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 1rem;

            padding:
                1rem
                1.25rem;

            border-bottom:
                1px solid
                var(--pm-border);
        }

        .pm-card-header h5 {

            margin: 0;

            color: var(--pm-navy);

            font-size: .95rem;

            font-weight: 700;
        }

        .pm-card-body {
            padding: 1.25rem;
        }


        /* =====================================================
           BUTTONS
           ===================================================== */

        .pm-btn-primary {

            border:
                1px solid
                var(--pm-teal);

            background:
                var(--pm-teal);

            color: #FFFFFF;
        }

        .pm-btn-primary:hover {

            border-color:
                var(--pm-teal-dark);

            background:
                var(--pm-teal-dark);

            color: #FFFFFF;
        }

        .pm-btn-success {

            border:
                1px solid
                var(--pm-green);

            background:
                var(--pm-green);

            color: #FFFFFF;
        }

        .pm-btn-success:hover {

            background: #15803D;

            color: #FFFFFF;
        }

        .pm-btn-dark {

            border:
                1px solid
                var(--pm-navy);

            background:
                var(--pm-navy);

            color: #FFFFFF;
        }

        .pm-btn-dark:hover {

            background:
                var(--pm-navy-dark);

            color: #FFFFFF;
        }

        .pm-btn-outline {

            border:
                1px solid
                var(--pm-border);

            background: #FFFFFF;

            color: var(--pm-navy);
        }

        .pm-btn-outline:hover {

            border-color: #CBD2DC;

            background: #F7F8FA;

            color: var(--pm-navy);
        }

        .pm-btn-outline-danger {

            border:
                1px solid
                rgba(220, 53, 69, .25);

            background: #FFFFFF;

            color: var(--pm-red);
        }

        .pm-btn-outline-danger:hover {

            background:
                rgba(220, 53, 69, .06);

            color: #B02A37;
        }

        .pm-btn-outline-warning {

            border:
                1px solid
                rgba(217, 119, 6, .25);

            background: #FFFFFF;

            color: var(--pm-amber);
        }

        .pm-btn-outline-warning:hover {

            background:
                rgba(217, 119, 6, .06);

            color: #B45309;
        }


        /* =====================================================
           DROPDOWNS
           ===================================================== */

        .dropdown-menu {

            border:
                1px solid
                var(--pm-border);

            border-radius: 11px;

            padding: .4rem;

            box-shadow:
                0 12px 35px
                rgba(15, 23, 42, .10);
        }

        .dropdown-item {

            border-radius: 7px;

            padding:
                .55rem
                .7rem;

            font-size: .84rem;
        }

        .dropdown-item:hover {

            background: #F3F6F8;
        }


        /* =====================================================
           SIDEBAR COLLAPSED
           ===================================================== */

        body.sidebar-collapsed .sidebar {

            width:
                var(--sidebar-collapsed-width);
        }

        body.sidebar-collapsed .sidebar-brand {

            justify-content: center;

            padding: 0;
        }

        body.sidebar-collapsed .sidebar-brand-link {

            width: auto;
        }

        body.sidebar-collapsed .sidebar-brand-text {
            display: none;
        }

        body.sidebar-collapsed .sidebar-section-label {
            display: none;
        }

        body.sidebar-collapsed .sidebar .nav-link {

            justify-content: center;

            padding-left: 0;

            padding-right: 0;
        }

        body.sidebar-collapsed .sidebar .nav-link span {
            display: none;
        }

        body.sidebar-collapsed .sidebar .nav-link i {
            margin-right: 0;
        }

        body.sidebar-collapsed .sidebar-user {

            justify-content: center;

            padding: .55rem;
        }

        body.sidebar-collapsed .sidebar-user-info,
        body.sidebar-collapsed .sidebar-user-arrow {
            display: none;
        }

        body.sidebar-collapsed .sidebar-footer {

            padding-left: .55rem;

            padding-right: .55rem;
        }

        body.sidebar-collapsed .sidebar-footer .sidebar-logout {

            justify-content: center;
        }


        /* =====================================================
           MOBILE OVERLAY
           ===================================================== */

        .sidebar-overlay {

            display: none;

            position: fixed;

            inset: 0;

            background:
                rgba(15, 23, 42, .45);

            z-index: 1030;
        }


        /* =====================================================
           FOOTER
           ===================================================== */

        .app-footer {

            padding:
                1rem
                1.5rem;

            color: #8A93A3;

            background: #FFFFFF;

            border-top:
                1px solid
                var(--pm-border);

            font-size: .75rem;

            text-align: center;
        }


        /* =====================================================
           MOBILE
           ===================================================== */

        @media (max-width: 991.98px) {

            .sidebar {

                width:
                    var(--sidebar-width);

                transform:
                    translateX(-100%);
            }

            body.sidebar-mobile-open .sidebar {

                transform:
                    translateX(0);
            }

            body.sidebar-mobile-open .sidebar-overlay {
                display: block;
            }

            .main-wrapper,
            body.sidebar-collapsed .main-wrapper {

                margin-left: 0;
            }

            .sidebar-toggle {
                display: none;
            }

            .mobile-menu-button {
                display: inline-flex;
            }

            .topbar {

                padding:
                    0
                    1rem;
            }

            .main-content {

                padding: 1rem;
            }

            .topbar-user-info {
                display: none;
            }

            .app-footer {

                padding:
                    .85rem
                    1rem;
            }

            body.sidebar-collapsed .sidebar {

                width:
                    var(--sidebar-width);
            }

            body.sidebar-collapsed .sidebar-brand {

                justify-content: flex-start;

                padding:
                    0
                    1.1rem;
            }

            body.sidebar-collapsed .sidebar-brand-text {
                display: flex;
            }

            body.sidebar-collapsed .sidebar-section-label {
                display: block;
            }

            body.sidebar-collapsed .sidebar .nav-link {

                justify-content: flex-start;

                padding:
                    .65rem
                    .75rem;
            }

            body.sidebar-collapsed .sidebar .nav-link span {
                display: inline;
            }

            body.sidebar-collapsed .sidebar .nav-link i {
                margin-right: .7rem;
            }

            body.sidebar-collapsed .sidebar-user {

                justify-content: flex-start;

                padding:
                    .65rem
                    .6rem;
            }

            body.sidebar-collapsed .sidebar-user-info {
                display: block;
            }

            body.sidebar-collapsed .sidebar-user-arrow {
                display: inline;
            }

            body.sidebar-collapsed .sidebar-footer {

                padding: .7rem;
            }
        }


        @media (max-width: 575.98px) {

            .topbar {

                height: 62px;
            }

            .notification-button {
                display: none;
            }

            .main-content {

                padding:
                    .85rem;
            }

            .pm-card-header {

                align-items: flex-start;

                flex-direction: column;
            }

        }

    </style>


    {{-- =========================================================
         PAGE-SPECIFIC STYLES
         ========================================================= --}}

    @stack('styles')

</head>


<body>


    {{-- =========================================================
         AUTHENTICATED APPLICATION
         ========================================================= --}}

    @auth

        @php

            /*
             * Current authenticated user.
             */
            $currentUser = auth()->user();


            /*
             * Generate initials for avatar fallback.
             *
             * Example:
             * John Doe -> JD
             * John -> J
             */
            $userInitials = collect(
                preg_split(
                    '/\s+/',
                    trim($currentUser->name ?? 'User')
                )
            )
                ->filter()
                ->map(
                    fn ($name) =>
                        strtoupper(
                            substr($name, 0, 1)
                        )
                )
                ->take(2)
                ->implode('');


            /*
             * Profile photo.
             *
             * profile_photo contains something like:
             *
             * profile-photos/abc123.jpg
             */
            $userProfilePhoto = $currentUser->profile_photo
                ? asset(
                    'storage/' .
                    $currentUser->profile_photo
                )
                : null;


            /*
             * Display role.
             */
            $displayRole = ucwords(
                str_replace(
                    '_',
                    ' ',
                    $currentUser->role ?? 'User'
                )
            );

        @endphp


        {{-- =====================================================
             SIDEBAR
             ===================================================== --}}

        <aside
            class="sidebar"
            id="appSidebar"
        >

            {{-- =================================================
                 BRAND
                 ================================================= --}}

            <div class="sidebar-brand">

                <a
                    href="{{ route('dashboard') }}"
                    class="sidebar-brand-link"
                >

                    <span class="sidebar-brand-icon">

                        <i class="bi bi-buildings"></i>

                    </span>

                    <span class="sidebar-brand-text">

                        <strong>
                            ImmoFlow
                        </strong>

                    </span>

                </a>

            </div>


            {{-- =================================================
                 NAVIGATION
                 ================================================= --}}

            <div
                class="sidebar-nav"
                id="sidebarNav"
            >

                {{-- =================================================
                     OVERVIEW
                     ================================================= --}}

                <div class="sidebar-section">

                    <div class="sidebar-section-label">
                        Overview
                    </div>

                    <a
                        href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    >

                        <i class="bi bi-grid-1x2-fill"></i>

                        <span>
                            Dashboard
                        </span>

                    </a>

                </div>


                {{-- =================================================
                     PROPERTY MANAGEMENT
                     ================================================= --}}

                @if(
                    $currentUser->hasRole([
                        'administrator',
                        'owner',
                        'property_manager',
                        'reception_staff'
                    ])
                )

                    <div class="sidebar-section">

                        <div class="sidebar-section-label">
                            Property Management
                        </div>


                        @if(!$currentUser->isOwner())
                            <a
                                href="{{ route('owners.index') }}"
                                class="nav-link {{ request()->routeIs('owners.*') ? 'active' : '' }}"
                            >

                                <i class="bi bi-person-badge"></i>

                                <span>
                                    Owners
                                </span>

                            </a>
                        @endif


                        <a
                            href="{{ route('properties.index') }}"
                            class="nav-link {{ request()->routeIs('properties.*') ? 'active' : '' }}"
                        >

                            <i class="bi bi-buildings"></i>

                            <span>
                                Properties
                            </span>

                        </a>


                        <a
                            href="{{ route('units.index') }}"
                            class="nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}"
                        >

                            <i class="bi bi-door-open"></i>

                            <span>
                                Units
                            </span>

                        </a>


                        @if(
                            $currentUser->hasRole([
                                'administrator',
                                'property_manager'
                            ])
                        )

                            <a
                                href="{{ route('property-types.index') }}"
                                class="nav-link {{ request()->routeIs('property-types.*') ? 'active' : '' }}"
                            >

                                <i class="bi bi-tags"></i>

                                <span>
                                    Property Types
                                </span>

                            </a>

                        @endif

                    </div>

                @endif


                {{-- =================================================
                     TENANCY
                     ================================================= --}}

                @if(
                    $currentUser->hasRole([
                        'administrator',
                        'owner',
                        'property_manager',
                        'reception_staff'
                    ])
                )

                    <div class="sidebar-section">

                        <div class="sidebar-section-label">
                            Tenancy
                        </div>


                        <a
                            href="{{ route('tenants.index') }}"
                            class="nav-link {{ request()->routeIs('tenants.*') ? 'active' : '' }}"
                        >

                            <i class="bi bi-people"></i>

                            <span>
                                Tenants
                            </span>

                        </a>


                        <a
                            href="{{ route('leases.index') }}"
                            class="nav-link {{ request()->routeIs('leases.*') ? 'active' : '' }}"
                        >

                            <i class="bi bi-file-earmark-text"></i>

                            <span>
                                Leases
                            </span>

                        </a>

                    </div>

                @endif


                {{-- =================================================
                     FINANCE
                     ================================================= --}}

                @if(
                    $currentUser->hasRole([
                        'administrator',
                        'owner',
                        'accountant',
                        'property_manager'
                    ])
                )

                    <div class="sidebar-section">

                        <div class="sidebar-section-label">
                            Finance
                        </div>


                        <a
                            href="{{ route('rent-invoices.index') }}"
                            class="nav-link {{ request()->routeIs('rent-invoices.*') ? 'active' : '' }}"
                        >

                            <i class="bi bi-receipt"></i>

                            <span>
                                Rent Invoices
                            </span>

                        </a>


                        <a
                            href="{{ route('payments.index') }}"
                            class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}"
                        >

                            <i class="bi bi-wallet2"></i>

                            <span>
                                Payments
                            </span>

                        </a>

                    </div>

                @endif


                {{-- =================================================
                     MAINTENANCE
                     ================================================= --}}

                @if(
                    $currentUser->hasRole([
                        'administrator',
                        'owner',
                        'property_manager',
                        'maintenance_staff'
                    ])
                )

                    <div class="sidebar-section">

                        <div class="sidebar-section-label">
                            Maintenance
                        </div>


                        @if(!$currentUser->isOwner())
                            <a
                                href="{{ route('maintenance-categories.index') }}"
                                class="nav-link {{ request()->routeIs('maintenance-categories.*') ? 'active' : '' }}"
                            >

                                <i class="bi bi-list-check"></i>

                                <span>
                                    Categories
                                </span>

                            </a>
                        @endif


                        <a
                            href="{{ route('maintenance-requests.index') }}"
                            class="nav-link {{ request()->routeIs('maintenance-requests.*') ? 'active' : '' }}"
                        >

                            <i class="bi bi-tools"></i>

                            <span>
                                Requests
                            </span>

                        </a>


                        <a
                            href="{{ route('maintenance-expenses.index') }}"
                            class="nav-link {{ request()->routeIs('maintenance-expenses.*') ? 'active' : '' }}"
                        >

                            <i class="bi bi-cash-stack"></i>

                            <span>
                                Expenses
                            </span>

                        </a>

                    </div>

                @endif


                {{-- =================================================
                     REPORTING
                     ================================================= --}}

                @if(
                    $currentUser->hasRole([
                        'administrator',
                        'owner',
                        'property_manager',
                        'accountant'
                    ])
                )

                    <div class="sidebar-section">

                        <div class="sidebar-section-label">
                            Reporting
                        </div>


                        <a
                            href="{{ route('reports.index') }}"
                            class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                        >

                            <i class="bi bi-bar-chart-line"></i>

                            <span>
                                Reports
                            </span>

                        </a>

                    </div>

                @endif


                {{-- =================================================
                     ADMINISTRATION
                     ================================================= --}}

                @if(
                    $currentUser->hasRole('administrator')
                )

                    <div class="sidebar-section">

                        <div class="sidebar-section-label">
                            Administration
                        </div>


                        <a
                            href="{{ route('users.index') }}"
                            class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                        >

                            <i class="bi bi-person-gear"></i>

                            <span>
                                Users
                            </span>

                        </a>

                    </div>

                @endif

            </div>


            {{-- =================================================
                 SIDEBAR FOOTER
                 ================================================= --}}

            <div class="sidebar-footer">


                {{-- =================================================
                     SETTINGS
                     ================================================= --}}

                <a
                    href="{{ route('settings.index') }}"
                    class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}"
                >

                    <i class="bi bi-gear"></i>

                    <span>
                        Settings
                    </span>

                </a>


                {{-- =================================================
                     HELP
                     ================================================= --}}

                <a
                    href="{{ route('support.index') }}"
                    class="nav-link {{ request()->routeIs('support.*') ? 'active' : '' }}"
                >

                    <i class="bi bi-question-circle"></i>

                    <span>
                        Help & Support
                    </span>

                </a>


                <div class="sidebar-divider"></div>


                {{-- =================================================
                     PROFILE
                     ================================================= --}}

                <a
                    href="{{ route('profile.edit') }}"
                    class="sidebar-user {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                    title="My Profile"
                >

                    <div class="sidebar-avatar">

                        @if($userProfilePhoto)

                            <img
                                src="{{ $userProfilePhoto }}"
                                alt="{{ $currentUser->name }}"
                            >

                        @else

                            {{ $userInitials ?: 'U' }}

                        @endif

                    </div>


                    <div class="sidebar-user-info">

                        <div class="sidebar-user-name">

                            {{ $currentUser->name }}

                        </div>


                        <div class="sidebar-user-role">

                            {{ $displayRole }}

                        </div>

                    </div>


                    <i
                        class="bi bi-chevron-right sidebar-user-arrow"
                    ></i>

                </a>


                {{-- =================================================
                     LOGOUT
                     ================================================= --}}

                <form
                    method="POST"
                    action="{{ route('logout') }}"
                    class="sidebar-logout-form"
                >

                    @csrf

                    <button
                        type="submit"
                        class="nav-link sidebar-logout"
                    >

                        <i class="bi bi-box-arrow-right"></i>

                        <span>
                            Logout
                        </span>

                    </button>

                </form>

            </div>

        </aside>


        {{-- =====================================================
             MOBILE OVERLAY
             ===================================================== --}}

        <div
            class="sidebar-overlay"
            id="sidebarOverlay"
        ></div>

    @endauth



    {{-- =========================================================
         MAIN WRAPPER
         ========================================================= --}}

    <div class="main-wrapper">


        {{-- =====================================================
             TOPBAR
             ===================================================== --}}

        @auth

            <header class="topbar">

                <div class="topbar-left">


                    {{-- MOBILE MENU --}}

                    <button
                        type="button"
                        class="mobile-menu-button"
                        id="mobileMenuToggle"
                        aria-label="Open navigation"
                    >

                        <i class="bi bi-list"></i>

                    </button>


                    {{-- DESKTOP SIDEBAR TOGGLE --}}

                    <button
                        type="button"
                        class="sidebar-toggle"
                        id="sidebarToggle"
                        aria-label="Toggle sidebar"
                    >

                        <i class="bi bi-list"></i>

                    </button>


                    {{-- PAGE TITLE --}}

                    <div class="topbar-heading">

                        <h1>

                            @yield(
                                'page-heading',
                                'Dashboard'
                            )

                        </h1>

                    </div>

                </div>


                <div class="topbar-right">

                    {{-- Language Switcher Dropdown --}}
                    @php
                        $currentLang = app()->getLocale();
                    @endphp
                    <div class="dropdown me-2">
                        <button
                            type="button"
                            class="notification-button d-inline-flex align-items-center gap-1"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            title="Switch Language"
                            style="width: auto; padding: 0 0.65rem; border-radius: 0.65rem; font-size: 0.82rem; font-weight: 700;"
                        >
                            <i class="bi bi-translate text-teal"></i>
                            <span>{{ strtoupper($currentLang) }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" style="border-radius: 0.75rem; padding: 0.4rem;">
                            <li>
                                <a class="dropdown-item d-flex align-items-center justify-content-between rounded py-2 px-3 {{ $currentLang === 'en' ? 'bg-light text-teal fw-bold' : '' }}" href="{{ route('lang.switch', 'en') }}">
                                    <span>🇬🇧 English</span>
                                    @if($currentLang === 'en') <i class="bi bi-check2 text-teal ms-2"></i> @endif
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center justify-content-between rounded py-2 px-3 {{ $currentLang === 'fr' ? 'bg-light text-teal fw-bold' : '' }}" href="{{ route('lang.switch', 'fr') }}">
                                    <span>🇫🇷 Français</span>
                                    @if($currentLang === 'fr') <i class="bi bi-check2 text-teal ms-2"></i> @endif
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- Theme Toggle Button --}}
                    <button
                        type="button"
                        class="notification-button me-2"
                        id="theme-toggle-btn"
                        title="Toggle Light/Dark Theme"
                        aria-label="Toggle Light/Dark Theme"
                        onclick="toggleAppTheme()"
                    >
                        <i class="bi bi-moon-stars" id="theme-toggle-icon"></i>
                    </button>

                    {{-- =================================================
                         NOTIFICATIONS
                         ================================================= --}}

                    @php
                        $unreadNotificationsCount = auth()->user()->unreadNotifications->count();
                        $latestNotifications = auth()->user()->notifications()->take(6)->get();
                    @endphp

                    <div class="dropdown">

                        <button
                            type="button"
                            class="notification-button position-relative"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            aria-label="Notifications"
                        >

                            <i class="bi bi-bell"></i>

                            @if($unreadNotificationsCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.65rem; transform: translate(-30%, 15%) !important;">
                                    {{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}
                                </span>
                            @endif

                        </button>


                        <div
                            class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-0"
                            style="width: 340px; max-width: 90vw; border-radius: 14px; overflow: hidden;"
                        >

                            <div class="px-3 py-2 border-bottom bg-light d-flex align-items-center justify-content-between">
                                <span class="fw-bold text-dark" style="font-size: 0.9rem;">
                                    Notifications
                                    @if($unreadNotificationsCount > 0)
                                        <span class="badge bg-primary rounded-pill ms-1" style="font-size: 0.7rem;">{{ $unreadNotificationsCount }} new</span>
                                    @endif
                                </span>
                                @if($unreadNotificationsCount > 0)
                                    <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-link btn-sm text-decoration-none p-0 text-primary" style="font-size: 0.78rem;">
                                            Mark all read
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <div class="notification-dropdown-list" style="max-height: 360px; overflow-y: auto;">
                                @forelse($latestNotifications as $notification)
                                    @php
                                        $data = $notification->data;
                                        $icon = $data['icon'] ?? 'bi-bell';
                                        $badgeColor = $data['badge_color'] ?? 'bg-primary';
                                        $isUnread = $notification->unread();
                                    @endphp
                                    <a 
                                        href="{{ route('notifications.read', $notification->id) }}" 
                                        class="dropdown-item p-3 border-bottom text-wrap d-flex align-items-start gap-2.5 transition-all {{ $isUnread ? 'bg-light' : '' }}"
                                        style="white-space: normal;"
                                    >
                                        <div class="rounded-circle {{ $badgeColor }} text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px; font-size: 0.95rem;">
                                            <i class="bi {{ $icon }}"></i>
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                                <span class="fw-semibold text-dark text-truncate" style="font-size: 0.85rem;">
                                                    {{ $data['title'] ?? 'Notification' }}
                                                </span>
                                                @if($isUnread)
                                                    <span class="badge bg-primary rounded-circle p-1" style="width: 7px; height: 7px;"></span>
                                                @endif
                                            </div>
                                            <div class="text-muted small mb-1" style="font-size: 0.78rem; line-height: 1.3;">
                                                {{ $data['message'] ?? '' }}
                                            </div>
                                            <div class="text-muted small" style="font-size: 0.7rem;">
                                                <i class="bi bi-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="p-4 text-center text-muted">
                                        <i class="bi bi-bell-slash text-secondary mb-2" style="font-size: 1.6rem;"></i>
                                        <div class="fw-semibold" style="font-size: 0.88rem;">No new notifications</div>
                                        <small style="font-size: 0.78rem;">You're all caught up!</small>
                                    </div>
                                @endforelse
                            </div>

                            <div class="p-2 border-top text-center bg-light">
                                <a href="{{ route('notifications.index') }}" class="fw-semibold text-primary text-decoration-none" style="font-size: 0.8rem;">
                                    View All Notifications <i class="bi bi-chevron-right small ms-1"></i>
                                </a>
                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                         USER DROPDOWN
                         ================================================= --}}

                    <div class="dropdown">

                        <button
                            type="button"
                            class="topbar-user"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            aria-label="Open user menu"
                        >

                            <div class="topbar-avatar">

                                @if($userProfilePhoto)

                                    <img
                                        src="{{ $userProfilePhoto }}"
                                        alt="{{ $currentUser->name }}"
                                    >

                                @else

                                    {{ $userInitials ?: 'U' }}

                                @endif

                            </div>


                            <div class="topbar-user-info">

                                <span class="topbar-user-name">

                                    {{ $currentUser->name }}

                                </span>


                                <span class="topbar-user-role">

                                    {{ $displayRole }}

                                </span>

                            </div>


                            <i
                                class="bi bi-chevron-down ms-1 text-muted"
                            ></i>

                        </button>


                        <ul
                            class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2"
                        >

                            {{-- USER INFO --}}

                            <li>

                                <div class="px-3 py-2">

                                    <div
                                        class="d-flex align-items-center gap-2"
                                    >

                                        <div class="topbar-avatar">

                                            @if($userProfilePhoto)

                                                <img
                                                    src="{{ $userProfilePhoto }}"
                                                    alt="{{ $currentUser->name }}"
                                                >

                                            @else

                                                {{ $userInitials ?: 'U' }}

                                            @endif

                                        </div>

                                        <div>

                                            <div class="fw-semibold">

                                                {{ $currentUser->name }}

                                            </div>

                                            <small class="text-muted">

                                                {{ $currentUser->email }}

                                            </small>

                                        </div>

                                    </div>

                                </div>

                            </li>


                            <li>

                                <hr class="dropdown-divider">

                            </li>


                            {{-- PROFILE --}}

                            <li>

                                <a
                                    class="dropdown-item"
                                    href="{{ route('profile.edit') }}"
                                >

                                    <i class="bi bi-person me-2"></i>

                                    Profile

                                </a>

                            </li>


                            {{-- SETTINGS --}}

                            <li>

                                <a
                                    class="dropdown-item"
                                    href="{{ route('settings.index') }}"
                                >

                                    <i class="bi bi-gear me-2"></i>

                                    Settings

                                </a>

                            </li>


                            <li>

                                <hr class="dropdown-divider">

                            </li>


                            {{-- LOGOUT --}}

                            <li>

                                <form
                                    method="POST"
                                    action="{{ route('logout') }}"
                                >

                                    @csrf

                                    <button
                                        type="submit"
                                        class="dropdown-item text-danger"
                                    >

                                        <i
                                            class="bi bi-box-arrow-right me-2"
                                        ></i>

                                        Logout

                                    </button>

                                </form>

                            </li>

                        </ul>

                    </div>

                </div>

            </header>

        @endauth



        {{-- =====================================================
             MAIN CONTENT
             ===================================================== --}}

        <main class="main-content">


            {{-- =================================================
                 SUCCESS
                 ================================================= --}}

            @if(session('success'))

                <div
                    class="alert alert-success alert-dismissible fade show pm-alert"
                    role="alert"
                >

                    <i class="bi bi-check-circle-fill"></i>

                    <div>

                        {{ session('success') }}

                    </div>


                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close"
                    ></button>

                </div>

            @endif


            {{-- =================================================
                 ERROR
                 ================================================= --}}

            @if(session('error'))

                <div
                    class="alert alert-danger alert-dismissible fade show pm-alert"
                    role="alert"
                >

                    <i class="bi bi-exclamation-circle-fill"></i>

                    <div>

                        {{ session('error') }}

                    </div>


                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close"
                    ></button>

                </div>

            @endif


            {{-- =================================================
                 VALIDATION ERRORS
                 ================================================= --}}

            @if($errors->any())

                <div
                    class="alert alert-danger alert-dismissible fade show pm-alert"
                    role="alert"
                >

                    <i class="bi bi-exclamation-triangle-fill"></i>

                    <div>

                        <strong>
                            Please correct the following errors:
                        </strong>


                        <ul class="mb-0 mt-1">

                            @foreach($errors->all() as $error)

                                <li>
                                    {{ $error }}
                                </li>

                            @endforeach

                        </ul>

                    </div>


                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close"
                    ></button>

                </div>

            @endif


            {{-- =================================================
                 PAGE CONTENT
                 ================================================= --}}

            @yield('content')

        </main>


        {{-- =====================================================
             FOOTER
             ===================================================== --}}

        @auth

            <footer class="app-footer">

                &copy; {{ date('Y') }}

                ImmoFlow.

                All rights reserved.

            </footer>

        @endauth

    </div>



    {{-- =========================================================
         BOOTSTRAP JAVASCRIPT
         ========================================================= --}}

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    ></script>



    {{-- =========================================================
         SIDEBAR JAVASCRIPT
         ========================================================= --}}

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const body =
                    document.body;

                const sidebar =
                    document.getElementById(
                        'appSidebar'
                    );

                const sidebarNav =
                    document.getElementById(
                        'sidebarNav'
                    );

                const sidebarToggle =
                    document.getElementById(
                        'sidebarToggle'
                    );

                const mobileMenuToggle =
                    document.getElementById(
                        'mobileMenuToggle'
                    );

                const sidebarOverlay =
                    document.getElementById(
                        'sidebarOverlay'
                    );


                /* =================================================
                   STORAGE KEYS
                   ================================================= */

                const sidebarScrollKey =
                    'property-management-sidebar-scroll';

                const sidebarCollapsedKey =
                    'sidebarCollapsed';


                /* =================================================
                   RESTORE SIDEBAR SCROLL
                   ================================================= */

                function restoreSidebarScroll() {

                    if (!sidebarNav) {
                        return;
                    }

                    const savedScroll =
                        localStorage.getItem(
                            sidebarScrollKey
                        );

                    if (
                        savedScroll === null
                    ) {
                        return;
                    }

                    const scrollPosition =
                        parseInt(
                            savedScroll,
                            10
                        );

                    if (
                        Number.isNaN(
                            scrollPosition
                        )
                    ) {
                        return;
                    }

                    requestAnimationFrame(
                        function () {

                            requestAnimationFrame(
                                function () {

                                    sidebarNav.scrollTop =
                                        scrollPosition;

                                }
                            );

                        }
                    );
                }


                /* =================================================
                   SAVE SIDEBAR SCROLL
                   ================================================= */

                function saveSidebarScroll() {

                    if (!sidebarNav) {
                        return;
                    }

                    localStorage.setItem(
                        sidebarScrollKey,
                        String(
                            sidebarNav.scrollTop
                        )
                    );
                }


                /* =================================================
                   RESTORE
                   ================================================= */

                restoreSidebarScroll();


                /* =================================================
                   SIDEBAR SCROLL EVENT
                   ================================================= */

                if (sidebarNav) {

                    sidebarNav.addEventListener(
                        'scroll',
                        saveSidebarScroll,
                        {
                            passive: true
                        }
                    );

                }


                /* =================================================
                   DESKTOP SIDEBAR COLLAPSE
                   ================================================= */

                if (
                    localStorage.getItem(
                        sidebarCollapsedKey
                    ) === 'true'
                    &&
                    window.innerWidth > 991
                ) {

                    body.classList.add(
                        'sidebar-collapsed'
                    );

                }


                if (sidebarToggle) {

                    sidebarToggle.addEventListener(
                        'click',
                        function () {

                            body.classList.toggle(
                                'sidebar-collapsed'
                            );

                            localStorage.setItem(
                                sidebarCollapsedKey,
                                body.classList.contains(
                                    'sidebar-collapsed'
                                )
                            );

                        }
                    );

                }


                /* =================================================
                   MOBILE SIDEBAR OPEN
                   ================================================= */

                if (mobileMenuToggle) {

                    mobileMenuToggle.addEventListener(
                        'click',
                        function () {

                            body.classList.add(
                                'sidebar-mobile-open'
                            );

                        }
                    );

                }


                /* =================================================
                   CLOSE MOBILE SIDEBAR
                   ================================================= */

                function closeMobileSidebar() {

                    body.classList.remove(
                        'sidebar-mobile-open'
                    );

                }


                if (sidebarOverlay) {

                    sidebarOverlay.addEventListener(
                        'click',
                        closeMobileSidebar
                    );

                }


                /* =================================================
                   SIDEBAR LINKS
                   ================================================= */

                if (sidebar) {

                    sidebar
                        .querySelectorAll(
                            'a[href]'
                        )
                        .forEach(
                            function (link) {

                                link.addEventListener(
                                    'click',
                                    function () {

                                        saveSidebarScroll();

                                        if (
                                            window.innerWidth <= 991
                                        ) {

                                            closeMobileSidebar();

                                        }

                                    }
                                );

                            }
                        );

                }


                /* =================================================
                   ESCAPE KEY
                   ================================================= */

                document.addEventListener(
                    'keydown',
                    function (event) {

                        if (
                            event.key === 'Escape'
                        ) {

                            closeMobileSidebar();

                        }

                    }
                );


                /* =================================================
                   RESIZE
                   ================================================= */

                window.addEventListener(
                    'resize',
                    function () {

                        if (
                            window.innerWidth > 991
                        ) {

                            closeMobileSidebar();

                        }

                    }
                );


                /* =================================================
                   BEFORE PAGE LEAVE
                   ================================================= */

                window.addEventListener(
                    'beforeunload',
                    saveSidebarScroll
                );

            }
        );

    </script>



    {{-- =========================================================
         PAGE-SPECIFIC SCRIPTS
         ========================================================= --}}

    @stack('scripts')

</body>

</html>
