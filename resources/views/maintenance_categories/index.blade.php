@extends('layouts.app')

@section('title', 'Maintenance Categories')
@section('page-heading', 'Maintenance Categories')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Maintenance Categories</h1>
            <p class="text-muted mb-0">
                Manage maintenance categories used to classify maintenance requests.
            </p>
        </div>

        <a href="{{ route('maintenance-categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            Add Category
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

        <div class="col-sm-6 col-xl-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary-subtle text-primary p-3 me-3">
                        <i class="bi bi-tags fs-4"></i>
                    </div>

                    <div>
                        <div class="text-muted small">Total Categories</div>
                        <div class="fs-4 fw-bold">
                            {{ $categories->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success-subtle text-success p-3 me-3">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>

                    <div>
                        <div class="text-muted small">Active Categories</div>
                        <div class="fs-4 fw-bold">
                            {{ $categories->where('is_active', true)->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-secondary-subtle text-secondary p-3 me-3">
                        <i class="bi bi-dash-circle fs-4"></i>
                    </div>

                    <div>
                        <div class="text-muted small">Inactive Categories</div>
                        <div class="fs-4 fw-bold">
                            {{ $categories->where('is_active', false)->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Category List --}}
    <div class="card shadow-sm border-0">

        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-tags me-2"></i>
                    Category List
                </h5>

                <span class="badge bg-light text-dark border">
                    {{ $categories->count() }} categories
                </span>
            </div>
        </div>

        @if($categories->count())

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($categories as $category)

                            <tr>

                                <td class="text-muted">
                                    {{ $category->id }}
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $category->name }}
                                    </div>
                                </td>

                                <td>
                                    <span class="text-muted">
                                        {{ $category->description ?: 'No description' }}
                                    </span>
                                </td>

                                <td>
                                    @if($category->is_active)
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
                                </td>

                                <td>
                                    <span class="text-muted">
                                        {{ $category->created_at?->format('d M Y') ?? '-' }}
                                    </span>
                                </td>

                                <td class="text-end">

                                    <div class="d-flex justify-content-end gap-1">

                                        <a href="{{ route('maintenance-categories.show', $category) }}"
                                           class="btn btn-sm btn-outline-info"
                                           title="View Category">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a href="{{ route('maintenance-categories.edit', $category) }}"
                                           class="btn btn-sm btn-outline-warning"
                                           title="Edit Category">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <form action="{{ route('maintenance-categories.destroy', $category) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this maintenance category?');">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Delete Category">
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
                    <i class="bi bi-tags display-4 text-muted"></i>
                </div>

                <h5 class="mb-2">No Maintenance Categories Found</h5>

                <p class="text-muted mb-4">
                    Create your first maintenance category to start organizing maintenance requests.
                </p>

                <a href="{{ route('maintenance-categories.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>
                    Add Category
                </a>

            </div>

        @endif

    </div>

</div>
@endsection