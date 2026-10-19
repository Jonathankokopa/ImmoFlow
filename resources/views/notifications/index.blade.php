
@extends('layouts.app')

@section('title', 'Notification Center')
@section('page-heading', 'Notifications Center')

@section('content')

<style>
    .notification-card {
        background: var(--pm-white, #ffffff);
        border: 1px solid var(--pm-border, #e2e8f0);
        border-radius: 14px;
        transition: all 0.2s ease;
    }
    .notification-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
    }
    .notification-card.unread {
        border-left: 4px solid #2563eb;
        background: rgba(37, 99, 235, 0.02);
    }
    .notification-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
</style>

<div class="container-fluid px-0">

    {{-- Header Banner & Actions --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Notifications</h4>
            <p class="text-muted small mb-0">Stay updated on payments, maintenance requests, leases, and tenant activities.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if(auth()->user()->unreadNotifications->count() > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
                        <i class="bi bi-check2-all me-1"></i> Mark All as Read
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Filter Navigation Tabs --}}
    <div class="mb-4">
        <ul class="nav nav-pills gap-2 border-bottom pb-3">
            <li class="nav-item">
                <a class="nav-link rounded-pill px-4 {{ $filter === 'all' ? 'active bg-primary text-white' : 'text-muted' }}" 
                   href="{{ route('notifications.index', ['filter' => 'all']) }}">
                   <i class="bi bi-bell me-1"></i> All Notifications 
                   <span class="badge bg-secondary ms-1">{{ auth()->user()->notifications()->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill px-4 {{ $filter === 'unread' ? 'active bg-primary text-white' : 'text-muted' }}" 
                   href="{{ route('notifications.index', ['filter' => 'unread']) }}">
                   <i class="bi bi-bell-fill me-1"></i> Unread 
                   <span class="badge bg-danger ms-1">{{ auth()->user()->unreadNotifications()->count() }}</span>
                </a>
            </li>
        </ul>
    </div>

    {{-- Notifications List --}}
    @if($notifications->count() > 0)
        <div class="d-flex flex-column gap-3 mb-4">
            @foreach($notifications as $notification)
                @php
                    $data = $notification->data;
                    $icon = $data['icon'] ?? 'bi-bell';
                    $badgeColor = $data['badge_color'] ?? 'bg-primary';
                    $isUnread = $notification->unread();
                @endphp
                <div class="notification-card p-3 p-md-4 {{ $isUnread ? 'unread' : '' }}">
                    <div class="d-flex align-items-start gap-3">
                        <div class="notification-icon-box {{ $badgeColor }} text-white">
                            <i class="bi {{ $icon }}"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                                <h6 class="fw-bold text-dark mb-0 text-truncate">
                                    {{ $data['title'] ?? 'Notification' }}
                                </h6>
                                <span class="text-muted small flex-shrink-0">
                                    <i class="bi bi-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <p class="text-muted mb-2 text-wrap" style="font-size: 0.9rem;">
                                {{ $data['message'] ?? '' }}
                            </p>
                            <div class="d-flex align-items-center gap-3">
                                @if(!empty($data['url']))
                                    <a href="{{ route('notifications.read', $notification->id) }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-medium">
                                        View Details <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                @endif
                                @if($isUnread)
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 small">Unread</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="bg-white border rounded-4 p-5 text-center my-4">
            <div class="mb-3">
                <i class="bi bi-bell-slash text-muted" style="font-size: 3rem;"></i>
            </div>
            <h5 class="fw-bold mb-1">No Notifications Found</h5>
            <p class="text-muted small">You're all caught up! Check back later for system activity updates.</p>
        </div>
    @endif

</div>

@endsection
