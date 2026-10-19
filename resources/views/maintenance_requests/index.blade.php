@extends('layouts.app')

@section('title', 'Maintenance Requests')
@section('page-heading', 'Maintenance Requests')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Maintenance Requests</h1>
            <p class="text-muted mb-0">
                Manage and track maintenance requests across your properties.
            </p>
        </div>

        <a href="{{ route('maintenance-requests.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            Add Request
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">

        {{-- Total Requests --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary-subtle text-primary p-3 me-3">
                        <i class="bi bi-tools fs-4"></i>
                    </div>

                    <div>
                        <div class="text-muted small">Total Requests</div>
                        <div class="fs-4 fw-bold">
                            {{ $requests->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Urgent --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-danger-subtle text-danger p-3 me-3">
                        <i class="bi bi-exclamation-triangle fs-4"></i>
                    </div>

                    <div>
                        <div class="text-muted small">Urgent</div>
                        <div class="fs-4 fw-bold">
                            {{ $requests->where('priority', 'urgent')->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Open --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-warning-subtle text-warning p-3 me-3">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>

                    <div>
                        <div class="text-muted small">Open</div>
                        <div class="fs-4 fw-bold">
                            {{ $requests->where('status', 'open')->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Completed --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success-subtle text-success p-3 me-3">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>

                    <div>
                        <div class="text-muted small">Completed</div>
                        <div class="fs-4 fw-bold">
                            {{ $requests->where('status', 'completed')->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Request List --}}
    <div class="card shadow-sm border-0">

        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-tools me-2"></i>
                    Maintenance Request List
                </h5>

                <span class="badge bg-light text-dark border">
                    {{ $requests->count() }} requests
                </span>
            </div>
        </div>

        @if($requests->count())

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Property</th>
                            <th>Unit</th>
                            <th>Tenant</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Requested Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($requests as $request)

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
                                    @if($request->unit?->property)
                                        <a href="{{ route('properties.show', $request->unit->property) }}"
                                           class="text-decoration-none">
                                            {{ $request->unit->property->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
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
                                    @if($request->tenant)
                                        <a href="{{ route('tenants.show', $request->tenant) }}"
                                           class="text-decoration-none">
                                            {{ $request->tenant->full_name }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    @if($request->category)
                                        {{ $request->category->name }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- Priority --}}
                                <td>
                                    @switch($request->priority)

                                        @case('urgent')
                                            <span class="badge bg-danger-subtle text-danger">
                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                Urgent
                                            </span>
                                            @break

                                        @case('high')
                                            <span class="badge bg-warning-subtle text-warning">
                                                <i class="bi bi-arrow-up-circle me-1"></i>
                                                High
                                            </span>
                                            @break

                                        @case('medium')
                                            <span class="badge bg-info-subtle text-info">
                                                <i class="bi bi-dash-circle me-1"></i>
                                                Medium
                                            </span>
                                            @break

                                        @default
                                            <span class="badge bg-secondary-subtle text-secondary">
                                                <i class="bi bi-arrow-down-circle me-1"></i>
                                                Low
                                            </span>

                                    @endswitch
                                </td>

                                {{-- Status --}}
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

                                {{-- Actions --}}
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">

                                        <a href="{{ route('maintenance-requests.show', $request) }}"
                                           class="btn btn-sm btn-outline-info"
                                           title="View Request">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a href="{{ route('maintenance-requests.edit', $request) }}"
                                           class="btn btn-sm btn-outline-warning"
                                           title="Edit Request">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <form action="{{ route('maintenance-requests.destroy', $request) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this maintenance request?');">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Delete Request">
                                                <i class="bi bi-trash"></i>
                                            </button>

                                        </form>

                                    </div>
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

                <h5 class="mb-2">No Maintenance Requests Found</h5>

                <p class="text-muted mb-4">
                    Create your first maintenance request to start tracking property issues.
                </p>

                <a href="{{ route('maintenance-requests.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>
                    Add Request
                </a>

            </div>

        @endif

    </div>

</div>
@endsection