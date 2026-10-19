@extends('portal.layout')

@section('title', 'Maintenance')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Maintenance</h1>
            <p class="text-muted mb-0">
                View and track your maintenance requests.
            </p>
        </div>

        <a
            href="{{ route('portal.maintenance.create') }}"
            class="btn btn-primary"
        >
            <i class="bi bi-plus-lg me-1"></i>
            New Request
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>
    @endif

    @if($requests->count())
        <div class="card portal-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">

                        <thead>
                            <tr>
                                <th class="px-4">Request</th>
                                <th>Category</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Requested</th>
                                <th class="text-end px-4">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($requests as $request)
                                @php
                                    $statusClass = match($request->status) {
                                        'completed',
                                        'resolved' => 'success',

                                        'in_progress',
                                        'assigned' => 'primary',

                                        'cancelled',
                                        'rejected' => 'danger',

                                        default => 'warning',
                                    };

                                    $priorityClass = match($request->priority) {
                                        'urgent' => 'danger',
                                        'high' => 'warning',
                                        'medium' => 'primary',
                                        default => 'secondary',
                                    };
                                @endphp

                                <tr>
                                    <td class="px-4">
                                        <div class="fw-semibold">
                                            {{ $request->title }}
                                        </div>

                                        <small class="text-muted">
                                            Request #{{ $request->id }}
                                        </small>
                                    </td>

                                    <td>
                                        {{ $request->category?->name ?? '—' }}
                                    </td>

                                    <td>
                                        <span class="badge bg-{{ $priorityClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $request->priority)) }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge bg-{{ $statusClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $request->requested_date
                                            ? $request->requested_date->format('d M Y')
                                            : '—'
                                        }}
                                    </td>

                                    <td class="text-end px-4">
                                        <a
                                            href="{{ route('portal.maintenance.show', $request) }}"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            <i class="bi bi-eye me-1"></i>
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4">
            {{ $requests->links() }}
        </div>
    @else
        <div class="card portal-card">
            <div class="card-body text-center py-5">

                <div class="mb-3">
                    <i
                        class="bi bi-tools"
                        style="font-size: 3rem;"
                    ></i>
                </div>

                <h5>No maintenance requests</h5>

                <p class="text-muted mb-4">
                    You have not submitted any maintenance requests yet.
                </p>

                <a
                    href="{{ route('portal.maintenance.create') }}"
                    class="btn btn-primary"
                >
                    <i class="bi bi-plus-lg me-1"></i>
                    Submit a Request
                </a>

            </div>
        </div>
    @endif

</div>
@endsection