<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\GeneralTrait;

class NotificationController extends Controller
{
    use GeneralTrait;

    public function getNotifications()
    {
        $notifications = auth()->user()->notifications;
        
        return $this->apiSuccessResponse($notifications);
    } //end of getNotifications
}
