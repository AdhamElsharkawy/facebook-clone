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
        $notifications = Notification::where('user_id', auth('api')->user()->id)->latest()->paginate(10);
        return $this->apiSuccessResponse($notifications);
    } //end of getNotifications
}
