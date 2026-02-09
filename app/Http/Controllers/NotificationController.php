<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /** Show current user's notifications (e.g. room assigned for housekeepers). */
    public function index(Request $request): View
    {
        $user = $request->user();
        $notifications = $user->notifications()->paginate(20);
        $user->unreadNotifications->markAsRead();

        return view('notifications.index', compact('notifications'));
    }

    /** Mark notification as read and redirect to its related URL. */
    public function readAndRedirect(Request $request, string $id): RedirectResponse|Redirector
    {
        $notification = DatabaseNotification::where('id', $id)->where('notifiable_id', $request->user()?->id)->firstOrFail();
        $url = $notification->data['url'] ?? route('notifications.index');
        $notification->markAsRead();
        return redirect($url);
    }
}
