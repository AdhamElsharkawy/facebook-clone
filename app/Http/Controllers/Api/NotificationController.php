<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Notification;
use App\Events\NewNotification;
use App\Models\User;

class NotificationController extends Controller
{
    use GeneralTrait;

    public function getNotifications()
    {
        $notifications = Notification::where('user_id', auth('api')->user()->id)->latest()->paginate(10);
        $notifications->makeHidden(['user_id', 'updated_at']);

        return $this->apiSuccessResponse($notifications);
    } //end of getNotifications

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['read' => true]);
        return $this->apiSuccessResponse(null, 'Notification marked as read successfully');
    } //end of markAsRead

    public static function newNotification($user_id, $type, $post_id = null)
    {
        if ($user_id == auth('api')->user()->id) return;
        if ($post_id == null) return;

        $notification = Notification::create([
            'user_id' => $user_id,
            'type' => $type,
            'post_id' => $post_id,
            'causer_name' => auth('api')->user()->name
        ]);

        // broadcast(new NewNotification(User::find($user_id), $notification));
    } //end of newNotification
}
