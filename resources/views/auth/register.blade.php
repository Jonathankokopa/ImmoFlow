<x-guest-layout>
    <div class="text-center mb-4">
        <h4 class="fw-bold text-dark mb-1">Create Account</h4>
        <p class="text-muted small">Join ImmoFlow Property Operations System</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label fw-semibold text-dark small mb-1">Full Name</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 12px 0 0 12px;">
                    <i class="bi bi-person"></i>
                </span>
                <input id="name" class="form-control border-start-0" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="John Doe" style="border-radius: 0 12px 12px 0;">
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-1 text-danger small" />
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold text-dark small mb-1">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 12px 0 0 12px;">
                    <i class="bi bi-envelope"></i>
                </span>
                <input id="email" class="form-control border-start-0" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="name@company.com" style="border-radius: 0 12px 12px 0;">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-danger small" />
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label fw-semibold text-dark small mb-1">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 12px 0 0 12px;">
                    <i class="bi bi-lock"></i>
                </span>
                <input id="password" class="form-control border-start-0" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" style="border-radius: 0 12px 12px 0;">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-danger small" />
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label fw-semibold text-dark small mb-1">Confirm Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0" style="border-radius: 12px 0 0 12px;">
                    <i class="bi bi-check-circle"></i>
                </span>
                <input id="password_confirmation" class="form-control border-start-0" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" style="border-radius: 0 12px 12px 0;">
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-danger small" />
        </div>

        <button type="submit" class="btn btn-auth-primary">
            Create Account <i class="bi bi-arrow-right ms-1"></i>
        </button>

        <div class="mt-4 text-center text-muted small">
            Already have an account? <a href="{{ route('login') }}" class="text-primary fw-semibold text-decoration-none">Sign in</a>
        </div>
    </form>
</x-guest-layout>
