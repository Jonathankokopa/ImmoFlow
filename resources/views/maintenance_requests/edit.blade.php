@extends('layouts.app')

@section('title', 'Edit Maintenance Request')
@section('page-heading', 'Edit Maintenance Request')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Edit Maintenance Request</h1>
            <p class="text-muted mb-0">
                Update the maintenance request details, assignment, priority, and status.
            </p>
        </div>

        <a href="{{ route('maintenance-requests.index') }}"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Requests
        </a>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm">
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

    <form action="{{ route('maintenance-requests.update', $maintenanceRequest) }}"
          method="POST">

        @csrf
        @method('PUT')

        {{-- Request Assignment --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">
                <h5 class="mb-1">
                    <i class="bi bi-person-workspace me-2"></i>
                    Request Assignment
                </h5>

                <small class="text-muted">
                    Select the unit, tenant, maintenance category, and staff member responsible for the request.
                </small>
            </div>

            <div class="card-body">
                <div class="row">

                    {{-- Unit --}}
                    <div class="col-md-6 mb-3">
                        <label for="unit_id" class="form-label">
                            Unit <span class="text-danger">*</span>
                        </label>

                        <select name="unit_id"
                                id="unit_id"
                                class="form-select @error('unit_id') is-invalid @enderror"
                                required>

                            <option value="">Select Unit</option>

                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}"
                                    {{ old('unit_id', $maintenanceRequest->unit_id) == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->property->name ?? 'Unknown Property' }}
                                    - Unit {{ $unit->unit_number }}
                                </option>
                            @endforeach

                        </select>

                        @error('unit_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Tenant --}}
                    <div class="col-md-6 mb-3">
                        <label for="tenant_id" class="form-label">
                            Tenant
                        </label>

                        <select name="tenant_id"
                                id="tenant_id"
                                class="form-select @error('tenant_id') is-invalid @enderror">

                            <option value="">Select Tenant</option>

                            @foreach($tenants as $tenant)
                                <option value="{{ $tenant->id }}"
                                    {{ old('tenant_id', $maintenanceRequest->tenant_id) == $tenant->id ? 'selected' : '' }}>
                                    {{ $tenant->full_name }}
                                </option>
                            @endforeach

                        </select>

                        @error('tenant_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div class="col-md-6 mb-3">
                        <label for="category_id" class="form-label">
                            Maintenance Category
                        </label>

                        <select name="category_id"
                                id="category_id"
                                class="form-select @error('category_id') is-invalid @enderror">

                            <option value="">Select Category</option>

                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $maintenanceRequest->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach

                        </select>

                        @error('category_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Assigned To --}}
                    <div class="col-md-6 mb-3">
                        <label for="assigned_to" class="form-label">
                            Assign To
                        </label>

                        <select name="assigned_to"
                                id="assigned_to"
                                class="form-select @error('assigned_to') is-invalid @enderror">

                            <option value="">Not Assigned</option>

                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('assigned_to', $maintenanceRequest->assigned_to) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach

                        </select>

                        @error('assigned_to')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

        {{-- Request Details --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">
                <h5 class="mb-1">
                    <i class="bi bi-tools me-2"></i>
                    Request Details
                </h5>

                <small class="text-muted">
                    Update the maintenance issue and provide a clear description of the work required.
                </small>
            </div>

            <div class="card-body">

                {{-- Title --}}
                <div class="mb-3">
                    <label for="title" class="form-label">
                        Request Title <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="title"
                           id="title"
                           class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title', $maintenanceRequest->title) }}"
                           required>

                    @error('title')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="form-label">
                        Description <span class="text-danger">*</span>
                    </label>

                    <textarea name="description"
                              id="description"
                              class="form-control @error('description') is-invalid @enderror"
                              rows="5"
                              required>{{ old('description', $maintenanceRequest->description) }}</textarea>

                    @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>
        </div>

        {{-- Request Status --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">
                <h5 class="mb-1">
                    <i class="bi bi-activity me-2"></i>
                    Request Status
                </h5>

                <small class="text-muted">
                    Set the priority, current status, and important request dates.
                </small>
            </div>

            <div class="card-body">
                <div class="row">

                    {{-- Priority --}}
                    <div class="col-md-4 mb-3">
                        <label for="priority" class="form-label">
                            Priority <span class="text-danger">*</span>
                        </label>

                        <select name="priority"
                                id="priority"
                                class="form-select @error('priority') is-invalid @enderror"
                                required>

                            <option value="low"
                                {{ old('priority', $maintenanceRequest->priority) === 'low' ? 'selected' : '' }}>
                                Low
                            </option>

                            <option value="medium"
                                {{ old('priority', $maintenanceRequest->priority) === 'medium' ? 'selected' : '' }}>
                                Medium
                            </option>

                            <option value="high"
                                {{ old('priority', $maintenanceRequest->priority) === 'high' ? 'selected' : '' }}>
                                High
                            </option>

                            <option value="urgent"
                                {{ old('priority', $maintenanceRequest->priority) === 'urgent' ? 'selected' : '' }}>
                                Urgent
                            </option>

                        </select>

                        @error('priority')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label">
                            Status <span class="text-danger">*</span>
                        </label>

                        <select name="status"
                                id="status"
                                class="form-select @error('status') is-invalid @enderror"
                                required>

                            <option value="pending"
                                {{ old('status', $maintenanceRequest->status) === 'pending' ? 'selected' : '' }}>
                                Pending
                            </option>

                            <option value="assigned"
                                {{ old('status', $maintenanceRequest->status) === 'assigned' ? 'selected' : '' }}>
                                Assigned
                            </option>

                            <option value="in_progress"
                                {{ old('status', $maintenanceRequest->status) === 'in_progress' ? 'selected' : '' }}>
                                In Progress
                            </option>

                            <option value="completed"
                                {{ old('status', $maintenanceRequest->status) === 'completed' ? 'selected' : '' }}>
                                Completed
                            </option>

                            <option value="cancelled"
                                {{ old('status', $maintenanceRequest->status) === 'cancelled' ? 'selected' : '' }}>
                                Cancelled
                            </option>

                        </select>

                        @error('status')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Requested Date --}}
                    <div class="col-md-4 mb-3">
                        <label for="requested_date" class="form-label">
                            Requested Date <span class="text-danger">*</span>
                        </label>

                        <input type="date"
                               name="requested_date"
                               id="requested_date"
                               class="form-control @error('requested_date') is-invalid @enderror"
                               value="{{ old('requested_date', $maintenanceRequest->requested_date?->format('Y-m-d')) }}"
                               required>

                        @error('requested_date')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Completed Date --}}
                    <div class="col-md-6 mb-3">
                        <label for="completed_date" class="form-label">
                            Completed Date
                        </label>

                        <input type="date"
                               name="completed_date"
                               id="completed_date"
                               class="form-control @error('completed_date') is-invalid @enderror"
                               value="{{ old('completed_date', $maintenanceRequest->completed_date?->format('Y-m-d')) }}">

                        @error('completed_date')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

        {{-- Additional Notes --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">
                <h5 class="mb-1">
                    <i class="bi bi-journal-text me-2"></i>
                    Additional Notes
                </h5>

                <small class="text-muted">
                    Add any additional information relevant to this maintenance request.
                </small>
            </div>

            <div class="card-body">

                <label for="notes" class="form-label">
                    Notes
                </label>

                <textarea name="notes"
                          id="notes"
                          class="form-control @error('notes') is-invalid @enderror"
                          rows="4"
                          placeholder="Enter any additional notes...">{{ old('notes', $maintenanceRequest->notes) }}</textarea>

                @error('notes')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

            </div>
        </div>

        {{-- Form Actions --}}
        <div class="d-flex flex-wrap gap-2 mb-4">

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>
                Update Request
            </button>

            <a href="{{ route('maintenance-requests.show', $maintenanceRequest) }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-x-lg me-1"></i>
                Cancel
            </a>

        </div>

    </form>

</div>
@endsection