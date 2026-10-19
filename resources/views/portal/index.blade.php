@extends('layouts.app')

@section('title', 'Tenant Portal')
@section('page-heading', 'Tenant Portal')

@section('content')

<div class="container-fluid px-0">

    {{-- Welcome --}}
    <div class="mb-4">
        <h2 class="mb-1">
            Welcome, {{ $tenant->full_name }}
        </h2>

        <p class="text-muted mb-0">
            Here you can view your tenancy information and property details.
        </p>
    </div>


    {{-- Tenant Information --}}
    <div class="card mb-4">

        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-person me-2"></i>
                My Information
            </h5>
        </div>

        <div class="card-body">

            <div class="row g-4">

                <div class="col-md-6">
                    <div class="text-muted small">
                        Full Name
                    </div>

                    <div class="fw-semibold">
                        {{ $tenant->full_name }}
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="text-muted small">
                        Email
                    </div>

                    <div class="fw-semibold">
                        {{ $tenant->email ?? 'Not provided' }}
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="text-muted small">
                        Phone
                    </div>

                    <div class="fw-semibold">
                        {{ $tenant->phone ?? 'Not provided' }}
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="text-muted small">
                        Status
                    </div>

                    <div class="fw-semibold">
                        {{ ucfirst($tenant->status) }}
                    </div>
                </div>


                <div class="col-12">
                    <div class="text-muted small">
                        Address
                    </div>

                    <div class="fw-semibold">
                        {{ $tenant->address ?? 'Not provided' }}
                    </div>
                </div>

            </div>

        </div>
    </div>


    {{-- Tenancies --}}
    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-house-door me-2"></i>
                My Tenancies
            </h5>
        </div>

        <div class="card-body">

            @forelse($tenant->leases as $lease)

                <div class="border rounded p-3 mb-3">

                    <div class="row g-4">

                        {{-- Property --}}
                        <div class="col-md-6">

                            <div class="text-muted small">
                                Property
                            </div>

                            <div class="fw-semibold">
                                {{ $lease->unit?->property?->name ?? 'N/A' }}
                            </div>

                        </div>


                        {{-- Unit --}}
                        <div class="col-md-6">

                            <div class="text-muted small">
                                Unit
                            </div>

                            <div class="fw-semibold">

                                {{
                                    $lease->unit?->name
                                    ?? $lease->unit?->unit_number
                                    ?? 'N/A'
                                }}

                            </div>

                        </div>


                        {{-- Start Date --}}
                        <div class="col-md-6">

                            <div class="text-muted small">
                                Start Date
                            </div>

                            <div class="fw-semibold">

                                @if($lease->start_date)

                                    {{ \Illuminate\Support\Carbon::parse($lease->start_date)->format('d M Y') }}

                                @else

                                    N/A

                                @endif

                            </div>

                        </div>


                        {{-- End Date --}}
                        <div class="col-md-6">

                            <div class="text-muted small">
                                End Date
                            </div>

                            <div class="fw-semibold">

                                @if($lease->end_date)

                                    {{ \Illuminate\Support\Carbon::parse($lease->end_date)->format('d M Y') }}

                                @else

                                    N/A

                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            @empty

                <div class="text-muted text-center py-4">

                    <i class="bi bi-house-x fs-2 d-block mb-2"></i>

                    You currently have no tenancy records.

                </div>

            @endforelse

        </div>

    </div>

</div>

@endsection