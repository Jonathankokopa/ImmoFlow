@extends('portal.layout')

@section('title', 'Maintenance Request')

@section('page-heading', 'Maintenance Request')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                Maintenance Request
            </h2>

            <p class="text-muted mb-0">
                Request #{{ $maintenanceRequest->id }}
            </p>
        </div>

        <a
            href="{{ route('portal.maintenance') }}"
            class="btn btn-outline-secondary"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Back
        </a>

    </div>


    <div class="card portal-card">

        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-start mb-4">

                <div>

                    <h4 class="fw-bold mb-2">
                        {{ $maintenanceRequest->title }}
                    </h4>

                    <small class="text-muted">
                        Submitted
                        {{ $maintenanceRequest->created_at->diffForHumans() }}
                    </small>

                </div>


                <span class="badge bg-info text-dark px-3 py-2">

                    {{ ucfirst(str_replace(
                        '_',
                        ' ',
                        $maintenanceRequest->status
                    )) }}

                </span>

            </div>


            <div class="row g-4 mb-4">

                <div class="col-md-4">

                    <small class="text-muted d-block">
                        Category
                    </small>

                    <strong>
                        {{ $maintenanceRequest->category?->name ?? 'Uncategorized' }}
                    </strong>

                </div>


                <div class="col-md-4">

                    <small class="text-muted d-block">
                        Priority
                    </small>

                    <strong>
                        {{ ucfirst($maintenanceRequest->priority) }}
                    </strong>

                </div>


                <div class="col-md-4">

                    <small class="text-muted d-block">
                        Submitted
                    </small>

                    <strong>
                        {{ $maintenanceRequest->created_at->format('d M Y, H:i') }}
                    </strong>

                </div>

            </div>


            <hr>


            <h6 class="fw-bold mb-3">
                Description
            </h6>

            <div class="p-3 bg-light rounded">

                {!! nl2br(e($maintenanceRequest->description)) !!}

            </div>

        </div>

    </div>

</div>

@endsection