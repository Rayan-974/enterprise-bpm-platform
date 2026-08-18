<?php

namespace App\Http\Controllers;

use App\Models\WorkflowNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->workflowNotifications()->orderBy('created_at', 'desc')->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(int $id)
    {
        $notification = WorkflowNotification::where('user_id', Auth::id())->findOrFail($id);
        $notification->update(['read_at' => now()]);

        return back()->with('success', 'Notification marked as read.');
    }
}
