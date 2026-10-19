@extends('portal.layout')

@section('title', 'My Profile')

@section('page-heading', 'My Profile')

@section('content')

<div class="container-fluid">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                My Profile
            </h2>

            <p class="text-muted mb-0">
                Manage your personal and contact information.
            </p>
        </div>

        <div
            class="bg-primary bg-opacity-10 text-primary rounded-circle"
            style="
                width: 52px;
                height: 52px;
                display: flex;
                align-items: center;
                justify-content: center;
            "
        >
            <i class="bi bi-person fs-4"></i>
        </div>

    </div>


    {{-- PROFILE PHOTO --}}
    <div class="card portal-card mb-4">

        <div class="card-body p-4">

            <div class="d-flex align-items-center flex-wrap gap-4">

                {{-- CURRENT PHOTO --}}
                <div>

                    @if($user->profile_photo)

                        <img
                            src="{{ asset('storage/' . $user->profile_photo) }}"
                            alt="{{ $tenant->full_name }}"
                            class="rounded-circle shadow-sm"
                            style="
                                width: 110px;
                                height: 110px;
                                object-fit: cover;
                            "
                        >

                    @else

                        <div
                            class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm"
                            style="
                                width: 110px;
                                height: 110px;
                                font-size: 2.4rem;
                                font-weight: 600;
                            "
                        >
                            {{ strtoupper(substr($tenant->full_name, 0, 1)) }}
                        </div>

                    @endif

                </div>


                {{-- PHOTO INFORMATION --}}
                <div class="flex-grow-1">

                    <h5 class="fw-bold mb-1">
                        Profile Picture
                    </h5>

                    <p class="text-muted mb-3">
                        Upload a photo that will appear on your tenant portal account.
                    </p>


                    <div class="d-flex flex-wrap gap-2">

                        {{-- UPLOAD --}}
                        <form
                            method="POST"
                            action="{{ route('portal.profile.photo') }}"
                            enctype="multipart/form-data"
                        >

                            @csrf

                            <label
                                for="profile_photo"
                                class="btn btn-primary"
                            >
                                <i class="bi bi-camera me-1"></i>
                                Choose Photo
                            </label>

                            <input
                                type="file"
                                id="profile_photo"
                                name="profile_photo"
                                accept="image/jpeg,image/png,image/webp"
                                class="d-none"
                                onchange="this.form.submit()"
                            >

                        </form>


                        {{-- REMOVE --}}
                        @if($user->profile_photo)

                            <form
                                method="POST"
                                action="{{ route('portal.profile.photo.remove') }}"
                            >

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="btn btn-outline-danger"
                                    onclick="return confirm('Remove your profile picture?')"
                                >
                                    <i class="bi bi-trash me-1"></i>
                                    Remove
                                </button>

                            </form>

                        @endif

                    </div>

                    <small class="text-muted d-block mt-2">
                        JPG, PNG or WEBP. Maximum size: 2 MB.
                    </small>

                </div>

            </div>

        </div>

    </div>


    {{-- PERSONAL INFORMATION --}}
    <div class="card portal-card">

        <div class="card-header bg-white border-0 pt-4 px-4">

            <div class="d-flex align-items-center">

                <div
                    class="bg-primary bg-opacity-10 text-primary rounded p-3 me-3"
                >
                    <i class="bi bi-person-vcard fs-4"></i>
                </div>

                <div>

                    <h5 class="fw-bold mb-1">
                        Personal Information
                    </h5>

                    <p class="text-muted mb-0 small">
                        Keep your contact details up to date.
                    </p>

                </div>

            </div>

        </div>


        <div class="card-body px-4 pb-4">

            <form
                method="POST"
                action="{{ route('portal.profile.update') }}"
            >

                @csrf
                @method('PUT')


                <div class="row g-4">

                    {{-- FULL NAME --}}
                    <div class="col-md-6">

                        <label
                            for="full_name"
                            class="form-label fw-semibold"
                        >
                            Full Name
                        </label>

                        <input
                            type="text"
                            id="full_name"
                            name="full_name"
                            value="{{ old('full_name', $tenant->full_name) }}"
                            class="form-control @error('full_name') is-invalid @enderror"
                            required
                        >

                        @error('full_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- EMAIL --}}
                    <div class="col-md-6">

                        <label
                            for="email"
                            class="form-label fw-semibold"
                        >
                            Email Address
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $tenant->email) }}"
                            class="form-control @error('email') is-invalid @enderror"
                            required
                        >

                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- PHONE --}}
                    <div class="col-md-6">

                        <label
                            for="phone"
                            class="form-label fw-semibold"
                        >
                            Phone Number
                        </label>

                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $tenant->phone) }}"
                            class="form-control @error('phone') is-invalid @enderror"
                            required
                        >

                        @error('phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- NATIONAL ID --}}
                    <div class="col-md-6">

                        <label class="form-label fw-semibold">
                            National ID
                        </label>

                        <input
                            type="text"
                            value="{{ $tenant->national_id ?: 'Not provided' }}"
                            class="form-control"
                            disabled
                        >

                        <div class="form-text">
                            <i class="bi bi-lock me-1"></i>
                            Contact property management to change this information.
                        </div>

                    </div>


                    {{-- ADDRESS --}}
                    <div class="col-12">

                        <label
                            for="address"
                            class="form-label fw-semibold"
                        >
                            Address
                        </label>

                        <textarea
                            id="address"
                            name="address"
                            rows="3"
                            class="form-control @error('address') is-invalid @enderror"
                        >{{ old('address', $tenant->address) }}</textarea>

                        @error('address')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="col-12">
                        <hr>
                    </div>


                    {{-- EMERGENCY CONTACT --}}
                    <div class="col-12">

                        <div class="d-flex align-items-center mb-1">

                            <i class="bi bi-telephone-plus text-primary me-2"></i>

                            <h6 class="fw-bold mb-0">
                                Emergency Contact
                            </h6>

                        </div>

                        <p class="text-muted small mb-0">
                            Someone property management can contact in an emergency.
                        </p>

                    </div>


                    <div class="col-md-6">

                        <label
                            for="emergency_contact_name"
                            class="form-label fw-semibold"
                        >
                            Emergency Contact Name
                        </label>

                        <input
                            type="text"
                            id="emergency_contact_name"
                            name="emergency_contact_name"
                            value="{{ old('emergency_contact_name', $tenant->emergency_contact_name) }}"
                            class="form-control @error('emergency_contact_name') is-invalid @enderror"
                        >

                        @error('emergency_contact_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="col-md-6">

                        <label
                            for="emergency_contact_phone"
                            class="form-label fw-semibold"
                        >
                            Emergency Contact Phone
                        </label>

                        <input
                            type="text"
                            id="emergency_contact_phone"
                            name="emergency_contact_phone"
                            value="{{ old('emergency_contact_phone', $tenant->emergency_contact_phone) }}"
                            class="form-control @error('emergency_contact_phone') is-invalid @enderror"
                        >

                        @error('emergency_contact_phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- SAVE --}}
                    <div class="col-12">

                        <hr>

                        <div class="d-flex justify-content-end gap-2">

                            <a
                                href="{{ route('portal') }}"
                                class="btn btn-outline-secondary"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                <i class="bi bi-check-lg me-1"></i>
                                Save Changes
                            </button>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection