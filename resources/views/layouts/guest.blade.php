<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ImmoFlow') }}</title>

    <!-- Bootstrap 5.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC;
            color: #1E293B;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        .auth-glow {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.12) 0%, rgba(13, 148, 136, 0.05) 50%, transparent 70%);
            pointer-events: none;
        }

        .auth-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.06);
            padding: 40px;
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 10;
        }

        .auth-logo-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #0F172A 0%, #2563EB 100%);
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.25rem;
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25);
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid #CBD5E1;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: #2563EB;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .btn-auth-primary {
            background: #2563EB;
            color: #FFFFFF;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            border: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
        }

        .btn-auth-primary:hover {
            background: #1D4ED8;
            color: #FFFFFF;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

    <div class="auth-glow"></div>

    <div class="container py-4">
        <div class="d-flex flex-column align-items-center">
            
            {{-- Brand Header --}}
            <div class="mb-4 text-center">
                <a href="/" class="text-decoration-none d-inline-flex align-items-center gap-2">
                    <span class="auth-logo-icon">
                        <i class="bi bi-buildings"></i>
                    </span>
                    <span class="fw-bold text-dark fs-3" style="letter-spacing: -0.02em;">ImmoFlow</span>
                </a>
            </div>

            {{-- Auth Card Wrapper --}}
            <div class="auth-card">
                {{ $slot }}
            </div>

            <div class="mt-4 text-center text-muted small">
                &copy; {{ date('Y') }} ImmoFlow. All rights reserved.
            </div>

        </div>
    </div>

</body>
</html>
