<?php

namespace App\Services;

use App\Mail\NotificationMail;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Create a notification for a user and email it to them.
     */
    public static function send(User $user, string $title, string $body): Notification
    {
        $notification = Notification::create([
            'user_id' => $user->id,
            'title'   => $title,
            'body'    => $body,
            'is_read' => false,
        ]);

        Mail::to($user->email)->send(new NotificationMail($notification));

        return $notification;
    }
}