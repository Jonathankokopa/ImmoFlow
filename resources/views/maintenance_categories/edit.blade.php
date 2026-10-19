@extends('layouts.app')

@section('title', 'Edit Maintenance Category')
@section('page-heading', 'Edit Maintenance Category')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Edit Maintenance Category</h1>
            <p class="text-muted mb-0">
                Update the maintenance category information below.
            </p>
        </div>

        <a href="{{ route('maintenance-categories.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Categories
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

    <form action="{{ route('maintenance-categories.update', $maintenanceCategory) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Basic Information --}}
        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="bi bi-tag me-2"></i>
                    Basic Information
                </h5>
            </div>

            <div class="card-body">

                <div class="mb-3">
                    <label for="name" class="form-label">
                        Category Name <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $maintenanceCategory->name) }}"
                        placeholder="e.g. Plumbing"
                        required
                    >

                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">
                        Description
                    </label>

                    <textarea
                        name="description"
                        id="description"
                        class="form-control @error('description') is-invalid @enderror"
                        rows="4"
                        placeholder="Describe this maintenance category"
                    >{{ old('description', $maintenanceCategory->description) }}</textarea>

                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>

        {{-- Category Status --}}
        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="bi bi-toggle-on me-2"></i>
                    Category Status
                </h5>
            </div>

            <div class="card-body">

                <div class="form-check form-switch">
                    <input
                        type="checkbox"
                        name="is_active"
                        id="is_active"
                        value="1"
                        class="form-check-input @error('is_active') is-invalid @enderror"
                        {{ old('is_active', $maintenanceCategory->is_active) ? 'checked' : '' }}
                    >

                    <label for="is_active" class="form-check-label">
                        <strong>Active</strong>
                    </label>

                    <div class="form-text">
                        Active categories will be available when creating maintenance requests.
                    </div>

                    @error('is_active')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>

        {{-- Actions --}}
        <div class="d-flex flex-wrap gap-2 mb-4">

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-1"></i>
                Update Category
            </button>

            <a href="{{ route('maintenance-categories.show', $maintenanceCategory) }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i>
                Cancel
            </a>

        </div>

    </form>

</div>
@endsection