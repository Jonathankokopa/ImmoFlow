@extends('portal.layout')

@section('title', 'New Maintenance Request')

@section('content')
<div class="container-fluid px-0">

    <div class="mb-4">
        <a
            href="{{ route('portal.maintenance') }}"
            class="text-decoration-none"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Back to Maintenance
        </a>

        <h1 class="h3 mt-3 mb-1">
            New Maintenance Request
        </h1>

        <p class="text-muted mb-0">
            Tell us what needs attention in your unit.
        </p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-2">
                Please correct the following:
            </div>

            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card portal-card">
        <div class="card-body p-4">

            <form
                method="POST"
                action="{{ route('portal.maintenance.store') }}"
            >
                @csrf

                <div class="row g-4">

                    <div class="col-md-6">
                        <label
                            for="category_id"
                            class="form-label"
                        >
                            Category
                        </label>

                        <select
                            name="category_id"
                            id="category_id"
                            class="form-select @error('category_id') is-invalid @enderror"
                            required
                        >
                            <option value="">
                                Select category
                            </option>

                            @foreach($categories as $category)
                                <option
                                    value="{{ $category->id }}"
                                    @selected(old('category_id') == $category->id)
                                >
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

                    <div class="col-md-6">
                        <label
                            for="priority"
                            class="form-label"
                        >
                            Priority
                        </label>

                        <select
                            name="priority"
                            id="priority"
                            class="form-select @error('priority') is-invalid @enderror"
                            required
                        >
                            <option value="">
                                Select priority
                            </option>

                            <option
                                value="low"
                                @selected(old('priority') === 'low')
                            >
                                Low
                            </option>

                            <option
                                value="medium"
                                @selected(old('priority') === 'medium')
                            >
                                Medium
                            </option>

                            <option
                                value="high"
                                @selected(old('priority') === 'high')
                            >
                                High
                            </option>

                            <option
                                value="urgent"
                                @selected(old('priority') === 'urgent')
                            >
                                Urgent
                            </option>
                        </select>

                        @error('priority')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label
                            for="title"
                            class="form-label"
                        >
                            Request Title
                        </label>

                        <input
                            type="text"
                            name="title"
                            id="title"
                            class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title') }}"
                            placeholder="Example: Kitchen sink is leaking"
                            maxlength="255"
                            required
                        >

                        @error('title')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label
                            for="description"
                            class="form-label"
                        >
                            Description
                        </label>

                        <textarea
                            name="description"
                            id="description"
                            rows="6"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Describe the problem in detail..."
                            maxlength="5000"
                            required
                        >{{ old('description') }}</textarea>

                        @error('description')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">

                    <a
                        href="{{ route('portal.maintenance') }}"
                        class="btn btn-outline-secondary"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-send me-1"></i>
                        Submit Request
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>
@endsection