@extends('portal.layout')

@section('title', 'Notifications')

@section('page-heading', 'Notifications')

@section('content')

<div class="container-fluid">


    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                Notifications
            </h2>

            <p class="text-muted mb-0">
                Stay up to date with your tenancy and account.
            </p>

        </div>


        @if($notifications->count() > 0)

            <form
                method="POST"
                action="{{ route('portal.notifications.mark-all-as-read') }}"
            >

                @csrf

                <button
                    type="submit"
                    class="btn btn-outline-primary"
                >

                    <i class="bi bi-check2-all me-1"></i>

                    Mark All as Read

                </button>

            </form>

        @endif

    </div>


    {{-- Filters --}}
    <div class="card portal-card mb-4">

        <div class="card-body">

            <div class="d-flex gap-2 flex-wrap">

                <a
                    href="{{ route('portal.notifications', ['filter' => 'all']) }}"
                    class="btn
                        {{ $filter === 'all'
                            ? 'btn-primary'
                            : 'btn-outline-primary' }}"
                >

                    <i class="bi bi-list-ul me-1"></i>

                    All

                </a>


                <a
                    href="{{ route('portal.notifications', ['filter' => 'unread']) }}"
                    class="btn
                        {{ $filter === 'unread'
                            ? 'btn-primary'
                            : 'btn-outline-primary' }}"
                >

                    <i class="bi bi-envelope me-1"></i>

                    Unread

                </a>

            </div>

        </div>

    </div>


    {{-- Notifications --}}
    <div class="card portal-card">

        <div class="card-body p-0">


            @forelse($notifications as $notification)

                @php

                    $icon =
                        $notification->data['icon']
                        ?? 'bi-bell';

                    $badgeColor =
                        $notification->data['badge_color']
                        ?? 'bg-primary';

                    $title =
                        $notification->data['title']
                        ?? 'Notification';

                    $message =
                        $notification->data['message']
                        ?? '';

                @endphp


                <a
                    href="{{ route(
                        'portal.notifications.read',
                        $notification->id
                    ) }}"
                    class="text-decoration-none text-dark d-block"
                >

                    <div
                        class="p-4 border-bottom
                            {{ $notification->unread()
                                ? 'bg-light'
                                : '' }}"
                    >

                        <div class="d-flex align-items-start">


                            {{-- Icon --}}
                            <div
                                class="portal-notification-icon
                                    {{ $badgeColor }} me-3"
                            >

                                <i class="bi {{ $icon }}"></i>

                            </div>


                            {{-- Content --}}
                            <div class="flex-grow-1">


                                <div class="d-flex justify-content-between align-items-start">

                                    <div>

                                        <h6 class="mb-1 fw-bold">

                                            {{ $title }}

                                            @if($notification->unread())

                                                <span
                                                    class="badge bg-primary ms-2"
                                                >
                                                    New
                                                </span>

                                            @endif

                                        </h6>


                                        <p class="text-muted mb-2">

                                            {{ $message }}

                                        </p>

                                    </div>


                                    @if($notification->unread())

                                        <span
                                            class="text-primary"
                                            title="Unread"
                                        >

                                            <i
                                                class="bi bi-circle-fill"
                                                style="font-size: 8px;"
                                            ></i>

                                        </span>

                                    @endif

                                </div>


                                <small class="text-muted">

                                    <i class="bi bi-clock me-1"></i>

                                    {{ $notification->created_at->diffForHumans() }}

                                </small>

                            </div>


                            {{-- Arrow --}}
                            <div class="ms-3 text-muted">

                                <i class="bi bi-chevron-right"></i>

                            </div>

                        </div>

                    </div>

                </a>


            @empty


                <div class="text-center py-5">

                    <div
                        class="bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-3"
                        style="
                            width: 70px;
                            height: 70px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        "
                    >

                        <i class="bi bi-bell-slash fs-3"></i>

                    </div>


                    <h5 class="fw-bold mb-2">
                        No Notifications
                    </h5>


                    <p class="text-muted mb-0">

                        You don't have any notifications at the moment.

                    </p>

                </div>

            @endforelse


        </div>

    </div>


    {{-- Pagination --}}
    @if($notifications->hasPages())

        <div class="mt-4">

            {{ $notifications->links() }}

        </div>

    @endif

</div>

@endsection