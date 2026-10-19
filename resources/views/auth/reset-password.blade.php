<x-guest-layout>

    <div class="mb-4 text-center">
        <h2 class="fw-bold">
            Set Your Password
        </h2>

        <p class="text-muted mb-0">
            Create a password for your tenant portal account.
        </p>
    </div>

    @if (session('status'))
        <div class="alert alert-success mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        {{-- Password reset token --}}
        <input
            type="hidden"
            name="token"
            value="{{ $request->route('token') }}"
        >

        {{-- Email --}}
        <div class="mb-3">
            <label
                for="email"
                class="form-label"
            >
                Email Address
            </label>

            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email', $request->email) }}"
                required
                autofocus
                autocomplete="email"
                class="form-control @error('email') is-invalid @enderror"
            >

            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <label
                for="password"
                class="form-label"
            >
                New Password
            </label>

            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                class="form-control @error('password') is-invalid @enderror"
            >

            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="mb-4">
            <label
                for="password_confirmation"
                class="form-label"
            >
                Confirm New Password
            </label>

            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                class="form-control"
            >
        </div>

        {{-- Submit --}}
        <div class="d-grid">
            <button
                type="submit"
                class="btn btn-primary"
            >
                <i class="bi bi-key me-1"></i>
                Reset Password
            </button>
        </div>
    </form>

    <div class="text-center mt-4">
        <a
            href="{{ route('login') }}"
            class="text-decoration-none"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Back to Login
        </a>
    </div>

</x-guest-layout>