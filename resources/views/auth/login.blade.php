<x-guest-layout>
    <div class="text-center mb-4">
        <h4 class="fw-bold text-dark mb-1">Welcome Back</h4>
        <p class="text-muted small">Sign in to your ImmoFlow account</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold text-dark small mb-1">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 12px 0 0 12px;">
                    <i class="bi bi-envelope"></i>
                </span>
                <input id="email" class="form-control border-start-0" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="name@company.com" style="border-radius: 0 12px 12px 0;">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-danger small" />
        </div>

        <!-- Password -->
        <div class="mb-3">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <label for="password" class="form-label fw-semibold text-dark small mb-0">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-primary small text-decoration-none fw-medium" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 12px 0 0 12px;">
                    <i class="bi bi-lock"></i>
                </span>
                <input id="password" class="form-control border-start-0" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" style="border-radius: 0 12px 12px 0;">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-danger small" />
        </div>

        <!-- Remember Me -->
        <div class="form-check mb-4">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
            <label for="remember_me" class="form-check-label text-muted small">Keep me signed in</label>
        </div>

        <button type="submit" class="btn btn-auth-primary">
            Sign In <i class="bi bi-arrow-right ms-1"></i>
        </button>

        @if (Route::has('register'))
            <div class="mt-4 text-center text-muted small">
                Don't have an account? <a href="{{ route('register') }}" class="text-primary fw-semibold text-decoration-none">Create one</a>
            </div>
        @endif
    </form>
</x-guest-layout>
