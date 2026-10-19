@extends('layouts.app')

@section('title', 'Add Maintenance Request')
@section('page-heading', 'Add Maintenance Request')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Add Maintenance Request</h1>
            <p class="text-muted mb-0">
                Create and assign a maintenance request for a property unit.
            </p>
        </div>

        <a href="{{ route('maintenance-requests.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Requests
        </a>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <div class="d-flex align-items-start">
                <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>

                <div>
                    <strong>Please correct the following errors:</strong>

                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('maintenance-requests.store') }}" method="POST">
        @csrf

        {{-- Request Assignment --}}
        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="bi bi-building me-2"></i>
                    Request Assignment
                </h5>
            </div>

            <div class="card-body">
                <div class="row">

                    {{-- Unit --}}
                    <div class="col-md-6 mb-3">
                        <label for="unit_id" class="form-label">
                            Unit <span class="text-danger">*</span>
                        </label>

                        <select
                            name="unit_id"
                            id="unit_id"
                            class="form-select @error('unit_id') is-invalid @enderror"
                            required
                        >
                            <option value="">-- Select Unit --</option>

                            @foreach($units as $unit)
                                <option
                                    value="{{ $unit->id }}"
                                    {{ old('unit_id') == $unit->id ? 'selected' : '' }}
                                >
                                    {{ $unit->property->name ?? 'Unknown Property' }}
                                    - Unit {{ $unit->unit_number }}
                                </option>
                            @endforeach
                        </select>

                        @error('unit_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tenant --}}
                    <div class="col-md-6 mb-3">
                        <label for="tenant_id" class="form-label">
                            Tenant
                        </label>

                        <select
                            name="tenant_id"
                            id="tenant_id"
                            class="form-select @error('tenant_id') is-invalid @enderror"
                        >
                            <option value="">-- Select Tenant --</option>

                            @foreach($tenants as $tenant)
                                <option
                                    value="{{ $tenant->id }}"
                                    {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}
                                >
                                    {{ $tenant->full_name }}
                                </option>
                            @endforeach
                        </select>

                        @error('tenant_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div class="col-md-6 mb-3">
                        <label for="category_id" class="form-label">
                            Maintenance Category
                        </label>

                        <select
                            name="category_id"
                            id="category_id"
                            class="form-select @error('category_id') is-invalid @enderror"
                        >
                            <option value="">-- Select Category --</option>

                            @foreach($categories as $category)
                                <option
                                    value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}
                                >
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Assigned To --}}
                    <div class="col-md-6 mb-3">
                        <label for="assigned_to" class="form-label">
                            Assign To
                        </label>

                        <select
                            name="assigned_to"
                            id="assigned_to"
                            class="form-select @error('assigned_to') is-invalid @enderror"
                        >
                            <option value="">-- Not Assigned --</option>

                            @foreach($users as $user)
                                <option
                                    value="{{ $user->id }}"
                                    {{ old('assigned_to') == $user->id ? 'selected' : '' }}
                                >
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('assigned_to')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

        {{-- Request Details --}}
        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="bi bi-tools me-2"></i>
                    Request Details
                </h5>
            </div>

            <div class="card-body">

                {{-- Title --}}
                <div class="mb-3">
                    <label for="title" class="form-label">
                        Request Title <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        name="title"
                        id="title"
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title') }}"
                        placeholder="e.g. Leaking kitchen tap"
                        required
                    >

                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="mb-3">
                    <label for="description" class="form-label">
                        Description <span class="text-danger">*</span>
                    </label>

                    <textarea
                        name="description"
                        id="description"
                        class="form-control @error('description') is-invalid @enderror"
                        rows="4"
                        placeholder="Describe the maintenance problem"
                        required
                    >{{ old('description') }}</textarea>

                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>

        {{-- Request Status --}}
        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="bi bi-clipboard-check me-2"></i>
                    Request Status
                </h5>
            </div>

            <div class="card-body">
                <div class="row">

                    {{-- Priority --}}
                    <div class="col-md-4 mb-3">
                        <label for="priority" class="form-label">
                            Priority <span class="text-danger">*</span>
                        </label>

                        <select
                            name="priority"
                            id="priority"
                            class="form-select @error('priority') is-invalid @enderror"
                            required
                        >
                            <option value="low"
                                {{ old('priority', 'medium') === 'low' ? 'selected' : '' }}>
                                Low
                            </option>

                            <option value="medium"
                                {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>
                                Medium
                            </option>

                            <option value="high"
                                {{ old('priority', 'medium') === 'high' ? 'selected' : '' }}>
                                High
                            </option>

                            <option value="urgent"
                                {{ old('priority', 'medium') === 'urgent' ? 'selected' : '' }}>
                                Urgent
                            </option>
                        </select>

                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label">
                            Status <span class="text-danger">*</span>
                        </label>

                        <select
                            name="status"
                            id="status"
                            class="form-select @error('status') is-invalid @enderror"
                            required
                        >
                            <option value="pending"
                                {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>
                                Pending
                            </option>

                            <option value="assigned"
                                {{ old('status', 'pending') === 'assigned' ? 'selected' : '' }}>
                                Assigned
                            </option>

                            <option value="in_progress"
                                {{ old('status', 'pending') === 'in_progress' ? 'selected' : '' }}>
                                In Progress
                            </option>

                            <option value="completed"
                                {{ old('status', 'pending') === 'completed' ? 'selected' : '' }}>
                                Completed
                            </option>

                            <option value="cancelled"
                                {{ old('status', 'pending') === 'cancelled' ? 'selected' : '' }}>
                                Cancelled
                            </option>
                        </select>

                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Requested Date --}}
                    <div class="col-md-4 mb-3">
                        <label for="requested_date" class="form-label">
                            Requested Date <span class="text-danger">*</span>
                        </label>

                        <input
                            type="date"
                            name="requested_date"
                            id="requested_date"
                            class="form-control @error('requested_date') is-invalid @enderror"
                            value="{{ old('requested_date', date('Y-m-d')) }}"
                            required
                        >

                        @error('requested_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Completed Date --}}
                    <div class="col-md-6 mb-3">
                        <label for="completed_date" class="form-label">
                            Completed Date
                        </label>

                        <input
                            type="date"
                            name="completed_date"
                            id="completed_date"
                            class="form-control @error('completed_date') is-invalid @enderror"
                            value="{{ old('completed_date') }}"
                        >

                        @error('completed_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

        {{-- Additional Notes --}}
        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="bi bi-journal-text me-2"></i>
                    Additional Notes
                </h5>
            </div>

            <div class="card-body">

                <label for="notes" class="form-label">
                    Notes
                </label>

                <textarea
                    name="notes"
                    id="notes"
                    class="form-control @error('notes') is-invalid @enderror"
                    rows="4"
                    placeholder="Additional notes about this maintenance request"
                >{{ old('notes') }}</textarea>

                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

            </div>
        </div>

        {{-- Actions --}}
        <div class="d-flex flex-wrap gap-2 mb-4">

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-1"></i>
                Save Request
            </button>

            <a href="{{ route('maintenance-requests.index') }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i>
                Cancel
            </a>

        </div>

    </form>

</div>
@endsection