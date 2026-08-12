<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * List all notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest('created_at')
            ->get();

        return ApiResponse::response(
            200,
            'Notifications retrieved successfully',
            $notifications
        );
    }

    /**
     * Show a single notification (must belong to the authenticated user).
     */
    public function show(Request $request, Notification $notification)
    {
        $this->authorize('view', $notification);

        return ApiResponse::response(
            200,
            'Notification retrieved successfully',
            $notification
        );
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, Notification $notification)
    {
        $this->authorize('update', $notification);

        $notification->update(['is_read' => true]);

        return ApiResponse::response(
            200,
            'Notification marked as read',
            $notification
        );
    }

    /**
     * Delete a notification.
     */
    public function destroy(Request $request, Notification $notification)
    {
        $this->authorize('delete', $notification);

        $notification->delete();

        return ApiResponse::response(200, 'Notification deleted');
    }
}