<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
}
