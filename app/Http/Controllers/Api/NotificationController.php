<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\TouristNotification;

class NotificationController extends Controller
{

    public function getNotifications(int $userID)
    {
        return Notification::where('userID', $userID)
            ->latest()
            ->get();
    }

    public function markAsRead(int $id)
    {
        $notification = Notification::findOrFail($id);

        $notification->is_read = true;

        $notification->save();

        return response()->json([
            'message' => 'Notification marked as read'
        ]);
    }

    public function getTouristNotifications(int $touristID)
    {
        $notifications = TouristNotification::where('touristID', $touristID)
            ->latest()
            ->get();

        return response()->json([
            'notifications' => $notifications
        ]);
    }

    public function getTouristUnreadCount(int $touristID)
    {
        $count = TouristNotification::where('touristID', $touristID)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'count' => $count
        ]);
    }

    public function markTouristNotificationRead(int $id)
    {
        $notification = TouristNotification::find($id);

        if (!$notification) {
            return response()->json([
                'message' => 'Notification not found.'
            ], 404);
        }

        $notification->is_read = true;
        $notification->save();

        return response()->json([
            'message' => 'Notification marked as read.'
        ]);
    }

    public function markAllTouristNotificationsRead(int $touristID)
    {
        TouristNotification::where('touristID', $touristID)
            ->update([
                'is_read' => true
            ]);

        return response()->json([
            'message' => 'All notifications marked as read.'
        ]);
    }
    
}
