<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantNotificationController extends Controller
{
    /**
     * Display tenant notifications.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_unless(
            $user &&
            $user->is_active &&
            $user->isRole('tenant') &&
            $user->tenant_id,
            403
        );

        $filter = $request->query('filter', 'all');

        $query = $user->notifications();

        if ($filter === 'unread') {
            $query = $user->unreadNotifications();
        }

        $notifications = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('portal.notifications.index', [
            'user' => $user,
            'notifications' => $notifications,
            'filter' => $filter,
        ]);
    }

    /**
     * Mark a tenant notification as read.
     */
    public function read(string $id): RedirectResponse
    {
        $user = request()->user();

        abort_unless(
            $user &&
            $user->is_active &&
            $user->isRole('tenant') &&
            $user->tenant_id,
            403
        );

        $notification = $user
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        if ($notification->unread()) {
            $notification->markAsRead();
        }

        /*
        |--------------------------------------------------------------------------
        | Tenant-safe notification destinations
        |--------------------------------------------------------------------------
        |
        | Never blindly redirect a tenant to an arbitrary URL stored inside
        | notification data.
        |
        */

        $type = $notification->data['type'] ?? 'system';

        $allowedTypes = [
            'tenant',
            'payment',
            'maintenance',
            'support',
            'system',
        ];

        if (!in_array($type, $allowedTypes, true)) {
            return redirect()
                ->route('portal.notifications')
                ->with(
                    'error',
                    'This notification cannot be opened from the tenant portal.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Portal-safe URL
        |--------------------------------------------------------------------------
        */

        $targetUrl = $notification->data['url'] ?? null;

        if (!$targetUrl) {
            return redirect()
                ->route('portal.notifications');
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent tenant notifications from sending users to the old
        | administration portal.
        |--------------------------------------------------------------------------
        |
        | Tenant notification destinations should eventually be mapped to
        | tenant-specific routes.
        |
        */

        if (
            str_contains($targetUrl, '/users') ||
            str_contains($targetUrl, '/tenants') ||
            str_contains($targetUrl, '/properties') ||
            str_contains($targetUrl, '/units') ||
            str_contains($targetUrl, '/leases') ||
            str_contains($targetUrl, '/payments') ||
            str_contains($targetUrl, '/maintenance-requests') ||
            str_contains($targetUrl, '/reports') ||
            str_contains($targetUrl, '/settings')
        ) {
            return redirect()
                ->route('portal.notifications');
        }

        return redirect()->to($targetUrl);
    }

    /**
     * Mark all tenant notifications as read.
     */
    public function markAllAsRead(): RedirectResponse
    {
        $user = request()->user();

        abort_unless(
            $user &&
            $user->is_active &&
            $user->isRole('tenant') &&
            $user->tenant_id,
            403
        );

        $user->unreadNotifications->markAsRead();

        return back()->with(
            'success',
            'All notifications have been marked as read.'
        );
    }
}