<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ImmoFlow | Next-Gen Real Estate & Property Management</title>
    
    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --immo-primary: #0F172A;
            --immo-accent: #2563EB;
            --immo-teal: #0D9488;
            --immo-bg: #F8FAFC;
            --immo-card-bg: #FFFFFF;
            --immo-border: #E2E8F0;
            --immo-text: #1E293B;
            --immo-muted: #64748B;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--immo-bg);
            color: var(--immo-text);
            overflow-x: hidden;
        }

        .hero-glow {
            position: absolute;
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            width: 800px;
            height: 500px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.12) 0%, rgba(13, 148, 136, 0.05) 50%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        .navbar-brand-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #0F172A 0%, #2563EB 100%);
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .badge-pill-header {
            background: rgba(37, 99, 235, 0.08);
            color: var(--immo-accent);
            border: 1px solid rgba(37, 99, 235, 0.2);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .feature-card {
            background: var(--immo-card-bg);
            border: 1px solid var(--immo-border);
            border-radius: 20px;
            padding: 32px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            z-index: 1;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
            border-color: rgba(37, 99, 235, 0.3);
        }

        .feature-icon-wrapper {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 24px;
        }

        .icon-blue { background: rgba(37, 99, 235, 0.1); color: #2563EB; }
        .icon-teal { background: rgba(13, 148, 136, 0.1); color: #0D9488; }
        .icon-amber { background: rgba(217, 119, 6, 0.1); color: #D97706; }
        .icon-purple { background: rgba(147, 51, 234, 0.1); color: #9333EA; }

        .stat-banner-card {
            background: #0F172A;
            color: #fff;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 24px 48px rgba(15, 23, 42, 0.15);
        }

        .btn-immo-primary {
            background: #2563EB;
            color: #fff;
            border-radius: 12px;
            padding: 12px 28px;
            font-weight: 600;
            border: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
        }

        .btn-immo-primary:hover {
            background: #1D4ED8;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }

        .btn-immo-secondary {
            background: #FFFFFF;
            color: var(--immo-text);
            border-radius: 12px;
            padding: 12px 28px;
            font-weight: 600;
            border: 1px solid var(--immo-border);
            transition: all 0.2s ease;
        }

        .btn-immo-secondary:hover {
            background: #F1F5F9;
            color: var(--immo-text);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <div class="hero-glow"></div>

    {{-- Top Navbar --}}
    <nav class="navbar navbar-expand-lg pt-4 pb-3 relative z-10">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-dark fs-4" href="{{ url('/') }}">
                <span class="navbar-brand-icon">
                    <i class="bi bi-buildings"></i>
                </span>
                <span>ImmoFlow</span>
            </a>
            <div class="d-flex align-items-center gap-3 ms-auto">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-immo-primary">
                            Dashboard <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-immo-secondary">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-immo-primary">Get Started</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="py-5 text-center position-relative z-10">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-9 col-xl-8">
                    <span class="badge-pill-header d-inline-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-stars"></i> Next-Gen Real Estate & Property Operating System
                    </span>
                    <h1 class="display-4 fw-extrabold text-dark mb-4 leading-tight" style="font-weight: 800; letter-spacing: -0.03em;">
                        Streamline Property Operations with <span class="text-primary">ImmoFlow</span>
                    </h1>
                    <p class="lead text-muted mb-5 px-lg-4" style="font-size: 1.15rem; line-height: 1.7;">
                        Manage properties, track lease agreements, automate rent collection, resolve maintenance requests, and analyze financial performance in one unified platform.
                    </p>

                    <div class="d-flex flex-sm-row flex-column align-items-center justify-content-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-immo-primary btn-lg px-4">
                                <i class="bi bi-grid-1x2-fill me-2"></i> Open Dashboard
                            </a>
                        @else
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-immo-primary btn-lg px-4">
                                    Create Free Account <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            @endif
                            <a href="{{ route('login') }}" class="btn btn-immo-secondary btn-lg px-4">
                                Sign In
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-dark mb-2">Built for Modern Real Estate Teams</h2>
                <p class="text-muted">Everything you need to run your properties efficiently and transparently.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card h-100">
                        <div class="feature-icon-wrapper icon-blue">
                            <i class="bi bi-buildings"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Property & Units</h5>
                        <p class="text-muted small mb-0">Organize owners, multi-unit buildings, single units, occupancy statuses, and rental pricing seamlessly.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="feature-card h-100">
                        <div class="feature-icon-wrapper icon-teal">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Rent & Receipts</h5>
                        <p class="text-muted small mb-0">Track automated invoices, manual & M-Pesa payments, partial allocations, and instant receipts.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="feature-card h-100">
                        <div class="feature-icon-wrapper icon-amber">
                            <i class="bi bi-tools"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Maintenance Desk</h5>
                        <p class="text-muted small mb-0">Log tenant maintenance requests, assign staff, set priorities, and monitor expenses with full audit history.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="feature-card h-100">
                        <div class="feature-icon-wrapper icon-purple">
                            <i class="bi bi-bar-chart-line"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Financial Reports</h5>
                        <p class="text-muted small mb-0">Gain clarity with real-time revenue analytics, collection rates, net position tracking, and export options.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats Banner --}}
    <section class="py-5 mb-5">
        <div class="container">
            <div class="stat-banner-card text-center text-lg-start">
                <div class="row align-items-center g-4">
                    <div class="col-lg-5">
                        <h3 class="fw-bold text-white mb-2">Designed for Maximum Operational Efficiency</h3>
                        <p class="text-white-50 mb-0">Empower property managers, accountants, and tenants with instant notifications and real-time clarity.</p>
                    </div>
                    <div class="col-lg-7">
                        <div class="row text-center g-3">
                            <div class="col-4">
                                <div class="p-3 bg-white bg-opacity-10 rounded-4">
                                    <h3 class="fw-extrabold text-white mb-1">100%</h3>
                                    <small class="text-white-50">Automated Accounting</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-white bg-opacity-10 rounded-4">
                                    <h3 class="fw-extrabold text-white mb-1">Real-Time</h3>
                                    <small class="text-white-50">Notifications Center</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 bg-white bg-opacity-10 rounded-4">
                                    <h3 class="fw-extrabold text-white mb-1">24/7</h3>
                                    <small class="text-white-50">Tenant Portal Access</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="py-4 border-top text-center text-muted small">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} <strong>ImmoFlow</strong>. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
