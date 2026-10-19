@extends('portal.layout')

@section('title', 'My Tenancy')

@section('page-heading', 'My Tenancy')

@section('content')

<div class="container-fluid">


    {{-- ================================================================== --}}
    {{-- PAGE HEADER --}}
    {{-- ================================================================== --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                My Tenancy
            </h2>

            <p class="text-muted mb-0">
                View your tenancy, property, and unit information.
            </p>

        </div>


        <span class="badge bg-primary px-3 py-2">

            <i class="bi bi-house-door me-1"></i>

            Tenant

        </span>

    </div>


    {{-- ================================================================== --}}
    {{-- MY INFORMATION --}}
    {{-- ================================================================== --}}

    <div class="card portal-card mb-4">

        <div class="card-header bg-white py-3">

            <h5 class="mb-0 fw-bold">

                <i class="bi bi-person me-2 text-primary"></i>

                My Information

            </h5>

        </div>


        <div class="card-body p-4">

            <div class="row g-4">


                {{-- Full Name --}}
                <div class="col-md-6">

                    <div class="text-muted small mb-1">
                        Full Name
                    </div>

                    <div class="fw-semibold">
                        {{ $tenant->full_name }}
                    </div>

                </div>


                {{-- Email --}}
                <div class="col-md-6">

                    <div class="text-muted small mb-1">
                        Email
                    </div>

                    <div class="fw-semibold">

                        {{ $tenant->email ?: 'Not provided' }}

                    </div>

                </div>


                {{-- Phone --}}
                <div class="col-md-6">

                    <div class="text-muted small mb-1">
                        Phone
                    </div>

                    <div class="fw-semibold">

                        {{ $tenant->phone ?: 'Not provided' }}

                    </div>

                </div>


                {{-- Status --}}
                <div class="col-md-6">

                    <div class="text-muted small mb-1">
                        Status
                    </div>

                    <div>

                        @if($tenant->status === 'active')

                            <span class="badge bg-success">
                                Active
                            </span>

                        @else

                            <span class="badge bg-secondary">
                                {{ ucfirst($tenant->status) }}
                            </span>

                        @endif

                    </div>

                </div>


                {{-- Address --}}
                <div class="col-12">

                    <div class="text-muted small mb-1">
                        Address
                    </div>

                    <div class="fw-semibold">

                        {{ $tenant->address ?: 'Not provided' }}

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ================================================================== --}}
    {{-- TENANCIES --}}
    {{-- ================================================================== --}}

    <div class="card portal-card">

        <div class="card-header bg-white py-3">

            <div class="d-flex justify-content-between align-items-center">

                <h5 class="mb-0 fw-bold">

                    <i class="bi bi-house-door me-2 text-primary"></i>

                    My Tenancies

                </h5>


                <span class="badge bg-light text-dark">

                    {{ $tenant->leases->count() }}

                    {{ $tenant->leases->count() === 1
                        ? 'Record'
                        : 'Records' }}

                </span>

            </div>

        </div>


        <div class="card-body p-4">


            @forelse($tenant->leases as $lease)


                {{-- ====================================================== --}}
                {{-- LEASE CARD --}}
                {{-- ====================================================== --}}

                <div
                    class="border rounded-3 p-4 mb-3"
                    style="border-color: #e5e7eb !important;"
                >

                    <div class="row g-4">


                        {{-- Property --}}
                        <div class="col-md-6">

                            <div class="d-flex align-items-start">

                                <div
                                    class="bg-primary bg-opacity-10 text-primary rounded p-2 me-3"
                                >

                                    <i class="bi bi-buildings"></i>

                                </div>


                                <div>

                                    <div class="text-muted small mb-1">
                                        Property
                                    </div>

                                    <div class="fw-semibold">

                                        {{ $lease->unit?->property?->name ?? 'N/A' }}

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- Unit --}}
                        <div class="col-md-6">

                            <div class="d-flex align-items-start">

                                <div
                                    class="bg-success bg-opacity-10 text-success rounded p-2 me-3"
                                >

                                    <i class="bi bi-house"></i>

                                </div>


                                <div>

                                    <div class="text-muted small mb-1">
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

                            </div>

                        </div>


                        {{-- Start Date --}}
                        <div class="col-md-6">

                            <div class="d-flex align-items-start">

                                <div
                                    class="bg-info bg-opacity-10 text-info rounded p-2 me-3"
                                >

                                    <i class="bi bi-calendar-check"></i>

                                </div>


                                <div>

                                    <div class="text-muted small mb-1">
                                        Start Date
                                    </div>

                                    <div class="fw-semibold">

                                        @if($lease->start_date)

                                            {{
                                                \Illuminate\Support\Carbon::parse(
                                                    $lease->start_date
                                                )->format('d M Y')
                                            }}

                                        @else

                                            N/A

                                        @endif

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- End Date --}}
                        <div class="col-md-6">

                            <div class="d-flex align-items-start">

                                <div
                                    class="bg-warning bg-opacity-10 text-warning rounded p-2 me-3"
                                >

                                    <i class="bi bi-calendar-x"></i>

                                </div>


                                <div>

                                    <div class="text-muted small mb-1">
                                        End Date
                                    </div>

                                    <div class="fw-semibold">

                                        @if($lease->end_date)

                                            {{
                                                \Illuminate\Support\Carbon::parse(
                                                    $lease->end_date
                                                )->format('d M Y')
                                            }}

                                        @else

                                            N/A

                                        @endif

                                    </div>

                                </div>

                            </div>

                        </div>


                    </div>

                </div>


            @empty


                {{-- Empty State --}}
                <div class="text-center py-5">

                    <div
                        class="bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-3"
                        style="
                            width: 70px;
                            height: 70px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        "
                    >

                        <i class="bi bi-house-x fs-3"></i>

                    </div>


                    <h5 class="fw-bold mb-2">
                        No Tenancy Records
                    </h5>


                    <p class="text-muted mb-0">

                        You currently have no tenancy records.

                    </p>

                </div>


            @endforelse

        </div>

    </div>

</div>

@endsection