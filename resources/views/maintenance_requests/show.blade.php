@extends('layouts.app')

@section('title', 'Maintenance Request Details')
@section('page-heading', 'Maintenance Request Details')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Maintenance Request Details</h1>
            <p class="text-muted mb-0">
                View maintenance request information, assignment, status, and related expenses.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('maintenance-requests.edit', $maintenanceRequest) }}"
               class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>
                Edit Request
            </a>

            <a href="{{ route('maintenance-requests.index') }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Back to Requests
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">Request</div>
                            <h5 class="mb-0">
                                {{ $maintenanceRequest->title }}
                            </h5>
                        </div>

                        <div class="text-primary fs-4">
                            <i class="bi bi-tools"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">Priority</div>

                            @if($maintenanceRequest->priority === 'urgent')
                                <span class="badge bg-danger">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Urgent
                                </span>
                            @elseif($maintenanceRequest->priority === 'high')
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-exclamation-circle me-1"></i>
                                    High
                                </span>
                            @elseif($maintenanceRequest->priority === 'medium')
                                <span class="badge bg-info text-dark">
                                    <i class="bi bi-dash-circle me-1"></i>
                                    Medium
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="bi bi-circle me-1"></i>
                                    Low
                                </span>
                            @endif
                        </div>

                        <div class="text-warning fs-4">
                            <i class="bi bi-flag"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">Status</div>

                            @if($maintenanceRequest->status === 'pending')
                                <span class="badge bg-secondary">Pending</span>
                            @elseif($maintenanceRequest->status === 'assigned')
                                <span class="badge bg-primary">Assigned</span>
                            @elseif($maintenanceRequest->status === 'open')
                                <span class="badge bg-warning text-dark">Open</span>
                            @elseif($maintenanceRequest->status === 'in_progress')
                                <span class="badge bg-info text-dark">In Progress</span>
                            @elseif($maintenanceRequest->status === 'completed')
                                <span class="badge bg-success">Completed</span>
                            @elseif($maintenanceRequest->status === 'cancelled')
                                <span class="badge bg-danger">Cancelled</span>
                            @else
                                <span class="badge bg-secondary">
                                    {{ ucfirst(str_replace('_', ' ', $maintenanceRequest->status)) }}
                                </span>
                            @endif
                        </div>

                        <div class="text-info fs-4">
                            <i class="bi bi-activity"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">Expenses</div>
                            <h4 class="mb-0">
                                {{ $maintenanceRequest->expenses->count() }}
                            </h4>
                            <small class="text-muted">Recorded expenses</small>
                        </div>

                        <div class="text-success fs-4">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Request Information --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-info-circle me-2"></i>
                Request Information
            </h5>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="text-muted small mb-1">Title</div>
                    <div class="fw-semibold">
                        {{ $maintenanceRequest->title }}
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="text-muted small mb-1">Property</div>

                    @if($maintenanceRequest->unit?->property)
                        <a href="{{ route('properties.show', $maintenanceRequest->unit->property) }}"
                           class="text-decoration-none fw-semibold">
                            {{ $maintenanceRequest->unit->property->name }}
                        </a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="text-muted small mb-1">Unit</div>

                    @if($maintenanceRequest->unit)
                        <a href="{{ route('units.show', $maintenanceRequest->unit) }}"
                           class="text-decoration-none fw-semibold">
                            {{ $maintenanceRequest->unit->unit_number }}
                        </a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="text-muted small mb-1">Tenant</div>

                    @if($maintenanceRequest->tenant)
                        <a href="{{ route('tenants.show', $maintenanceRequest->tenant) }}"
                           class="text-decoration-none fw-semibold">
                            {{ $maintenanceRequest->tenant->full_name }}
                        </a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="text-muted small mb-1">Category</div>
                    <div class="fw-semibold">
                        {{ $maintenanceRequest->category->name ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="text-muted small mb-1">Assigned To</div>
                    <div class="fw-semibold">
                        {{ $maintenanceRequest->assignedTo->name ?? 'Not Assigned' }}
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="text-muted small mb-1">Priority</div>

                    @if($maintenanceRequest->priority === 'urgent')
                        <span class="badge bg-danger">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Urgent
                        </span>
                    @elseif($maintenanceRequest->priority === 'high')
                        <span class="badge bg-warning text-dark">
                            High
                        </span>
                    @elseif($maintenanceRequest->priority === 'medium')
                        <span class="badge bg-info text-dark">
                            Medium
                        </span>
                    @else
                        <span class="badge bg-secondary">
                            Low
                        </span>
                    @endif
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="text-muted small mb-1">Status</div>

                    @if($maintenanceRequest->status === 'pending')
                        <span class="badge bg-secondary">Pending</span>
                    @elseif($maintenanceRequest->status === 'assigned')
                        <span class="badge bg-primary">Assigned</span>
                    @elseif($maintenanceRequest->status === 'open')
                        <span class="badge bg-warning text-dark">Open</span>
                    @elseif($maintenanceRequest->status === 'in_progress')
                        <span class="badge bg-info text-dark">In Progress</span>
                    @elseif($maintenanceRequest->status === 'completed')
                        <span class="badge bg-success">Completed</span>
                    @elseif($maintenanceRequest->status === 'cancelled')
                        <span class="badge bg-danger">Cancelled</span>
                    @else
                        <span class="badge bg-secondary">
                            {{ ucfirst(str_replace('_', ' ', $maintenanceRequest->status)) }}
                        </span>
                    @endif
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="text-muted small mb-1">Requested Date</div>
                    <div class="fw-semibold">
                        {{ $maintenanceRequest->requested_date?->format('d M Y') ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="text-muted small mb-1">Completed Date</div>
                    <div class="fw-semibold">
                        {{ $maintenanceRequest->completed_date?->format('d M Y') ?? '-' }}
                    </div>
                </div>

                <div class="col-12 mb-4">
                    <div class="text-muted small mb-1">Description</div>

                    <div class="border rounded bg-light p-3">
                        {{ $maintenanceRequest->description }}
                    </div>
                </div>

                <div class="col-12">
                    <div class="text-muted small mb-1">Notes</div>

                    <div class="border rounded bg-light p-3">
                        {{ $maintenanceRequest->notes ?? '-' }}
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- Maintenance Expenses --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div>
                    <h5 class="mb-1">
                        <i class="bi bi-cash-stack me-2"></i>
                        Maintenance Expenses
                    </h5>

                    <small class="text-muted">
                        Expenses recorded against this maintenance request.
                    </small>
                </div>

                <span class="badge bg-light text-dark border">
                    {{ $maintenanceRequest->expenses->count() }} Expense(s)
                </span>
            </div>
        </div>

        <div class="card-body">

            @if($maintenanceRequest->expenses->count())

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($maintenanceRequest->expenses as $expense)

                                <tr>
                                    <td class="fw-semibold">
                                        {{ $expense->id }}
                                    </td>

                                    <td>
                                        {{ ucfirst(str_replace('_', ' ', $expense->expense_type)) }}
                                    </td>

                                    <td>
                                        {{ $expense->description ?: '-' }}
                                    </td>

                                    <td class="fw-semibold">
                                        {{ number_format($expense->amount, 2) }}
                                    </td>

                                    <td>
                                        {{ $expense->expense_date?->format('d M Y') ?? '-' }}
                                    </td>
                                </tr>

                            @endforeach

                        </tbody>

                    </table>
                </div>

            @else

                <div class="text-center py-5">
                    <div class="text-muted fs-1 mb-3">
                        <i class="bi bi-receipt"></i>
                    </div>

                    <h6 class="mb-1">No Maintenance Expenses</h6>

                    <p class="text-muted mb-0">
                        No maintenance expenses have been recorded for this request yet.
                    </p>
                </div>

            @endif

        </div>

    </div>

</div>
@endsection