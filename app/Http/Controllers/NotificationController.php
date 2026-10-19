<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display a listing of user notifications.
     */
    public function index(Request $request): View
    {
        $filter = $request->query('filter', 'all');
        $user = auth()->user();

        $query = $user->notifications();

        if ($filter === 'unread') {
            $query = $user->unreadNotifications();
        }

        $notifications = $query->paginate(15)->withQueryString();

        return view('notifications.index', compact('notifications', 'filter'));
    }

    /**
     * Mark a single notification as read and redirect to its target URL.
     */
    public function readAndRedirect(string $id): RedirectResponse
    {
        $user = auth()->user();

        $notification = $user->notifications()->where('id', $id)->firstOrFail();

        if ($notification->unread()) {
            $notification->markAsRead();
        }

        $targetUrl = $notification->data['url'] ?? route('notifications.index');

        return redirect()->to($targetUrl);
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read.');
    }
}
