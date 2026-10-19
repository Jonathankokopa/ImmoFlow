@extends('layouts.app')

@section('title', 'Maintenance Category Details')
@section('page-heading', 'Maintenance Category Details')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Maintenance Category Details</h1>
            <p class="text-muted mb-0">
                View category information and related maintenance requests.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('maintenance-categories.edit', $maintenanceCategory) }}"
               class="btn btn-outline-warning">
                <i class="bi bi-pencil me-1"></i>
                Edit Category
            </a>

            <a href="{{ route('maintenance-categories.index') }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Back to Categories
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">

        <div class="col-sm-6 col-xl-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary-subtle text-primary p-3 me-3">
                        <i class="bi bi-tag fs-4"></i>
                    </div>

                    <div>
                        <div class="text-muted small">Category</div>
                        <div class="fs-5 fw-bold">
                            {{ $maintenanceCategory->name }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-info-subtle text-info p-3 me-3">
                        <i class="bi bi-tools fs-4"></i>
                    </div>

                    <div>
                        <div class="text-muted small">Maintenance Requests</div>
                        <div class="fs-4 fw-bold">
                            {{ $maintenanceCategory->maintenanceRequests->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle {{ $maintenanceCategory->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} p-3 me-3">
                        <i class="bi {{ $maintenanceCategory->is_active ? 'bi-check-circle' : 'bi-dash-circle' }} fs-4"></i>
                    </div>

                    <div>
                        <div class="text-muted small">Status</div>

                        @if($maintenanceCategory->is_active)
                            <span class="badge bg-success-subtle text-success">
                                Active
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary">
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Category Information --}}
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-info-circle me-2"></i>
                Category Information
            </h5>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-6 mb-4">
                    <div class="text-muted small mb-1">Category Name</div>
                    <div class="fw-semibold">
                        {{ $maintenanceCategory->name }}
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="text-muted small mb-1">Status</div>

                    @if($maintenanceCategory->is_active)
                        <span class="badge bg-success-subtle text-success">
                            <i class="bi bi-check-circle me-1"></i>
                            Active
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary">
                            <i class="bi bi-dash-circle me-1"></i>
                            Inactive
                        </span>
                    @endif
                </div>

                <div class="col-md-12">
                    <div class="text-muted small mb-1">Description</div>

                    <div>
                        {{ $maintenanceCategory->description ?: 'No description provided.' }}
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- Maintenance Requests --}}
    <div class="card shadow-sm border-0">

        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">

                <h5 class="mb-0">
                    <i class="bi bi-tools me-2"></i>
                    Maintenance Requests
                </h5>

                <span class="badge bg-light text-dark border">
                    {{ $maintenanceCategory->maintenanceRequests->count() }} requests
                </span>

            </div>
        </div>

        @if($maintenanceCategory->maintenanceRequests->count())

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Unit</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Requested Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($maintenanceCategory->maintenanceRequests as $request)

                            <tr>

                                <td class="text-muted">
                                    {{ $request->id }}
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $request->title }}
                                    </div>
                                </td>

                                <td>
                                    @if($request->unit)
                                        <a href="{{ route('units.show', $request->unit) }}"
                                           class="text-decoration-none">
                                            {{ $request->unit->unit_number }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    @switch($request->priority)
                                        @case('high')
                                            <span class="badge bg-danger-subtle text-danger">
                                                High
                                            </span>
                                            @break

                                        @case('medium')
                                            <span class="badge bg-warning-subtle text-warning">
                                                Medium
                                            </span>
                                            @break

                                        @case('low')
                                            <span class="badge bg-success-subtle text-success">
                                                Low
                                            </span>
                                            @break

                                        @default
                                            <span class="badge bg-secondary-subtle text-secondary">
                                                {{ ucfirst($request->priority ?? 'Unknown') }}
                                            </span>
                                    @endswitch
                                </td>

                                <td>
                                    @switch($request->status)
                                        @case('open')
                                            <span class="badge bg-primary-subtle text-primary">
                                                Open
                                            </span>
                                            @break

                                        @case('in_progress')
                                            <span class="badge bg-warning-subtle text-warning">
                                                In Progress
                                            </span>
                                            @break

                                        @case('completed')
                                            <span class="badge bg-success-subtle text-success">
                                                Completed
                                            </span>
                                            @break

                                        @case('cancelled')
                                            <span class="badge bg-secondary-subtle text-secondary">
                                                Cancelled
                                            </span>
                                            @break

                                        @default
                                            <span class="badge bg-light text-dark border">
                                                {{ ucfirst(str_replace('_', ' ', $request->status ?? 'Unknown')) }}
                                            </span>
                                    @endswitch
                                </td>

                                <td>
                                    <span class="text-muted">
                                        {{ $request->requested_date?->format('d M Y') ?? '-' }}
                                    </span>
                                </td>

                                <td class="text-end">
                                    <a href="{{ route('maintenance-requests.show', $request) }}"
                                       class="btn btn-sm btn-outline-info"
                                       title="View Request">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="card-body text-center py-5">

                <div class="mb-3">
                    <i class="bi bi-tools display-4 text-muted"></i>
                </div>

                <h5 class="mb-2">No Maintenance Requests</h5>

                <p class="text-muted mb-0">
                    No maintenance requests have been recorded for this category yet.
                </p>

            </div>

        @endif

    </div>

</div>
@endsection