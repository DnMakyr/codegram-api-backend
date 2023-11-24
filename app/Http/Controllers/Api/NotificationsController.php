<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications;
        $unreadNotificationsCount = auth()->user()->unreadNotifications->count();
        return response()->json([
            'notifications' => $notifications,
            'unreadNotifications' => $unreadNotificationsCount
        ]);
    }
    public function markAsRead(Request $request)
    {
        $notification = auth()->user()->notifications()->where('id', $request->id)->first();
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => 'Marked as read']);
        } else {
            return response()->json(['error' => 'Notification not found']);
        }
    }
    public function markAllAsRead()
    {
        $notifications = auth()->user()->unreadNotifications;
        if ($notifications) {
            $notifications->markAsRead();
            return response()->json(['success' => 'Marked all as read']);
        } else {
            return response()->json(['error' => 'Has read all']);
        }
    }
}
