<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications;
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json([
            'notifications' => $notifications,
        ]);
    }
}
