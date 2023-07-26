<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Notification;

class NotificationController extends Controller
{
    use GeneralTrait;

    public function getNotifications()
    {
        $notifications = Notification::where('user_id', auth('api')->user()->id)->with("post.comments")->latest()->paginate(10);
        $notifications->makeHidden(['user_id', 'updated_at']);
        $notifications->map(function ($notification) {
            $notification->post->makeHidden(['images', 'user_id', 'updated_at']);
            $notification->post->comments->map(function ($comment) {
                $comment->makeHidden(['id', 'images','user_id', 'updated_at']);
            });
        });
        return $this->apiSuccessResponse($notifications);
    } //end of getNotifications

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['read' => true]);
        return $this->apiSuccessResponse(null, 'Notification marked as read successfully');
    } //end of markAsRead
}
