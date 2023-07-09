<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use Illuminate\Http\Request;

class EventController extends Controller
{
    use GeneralTrait;

    public function getEvents()
    {
        $events = \App\Models\Event::orderBy('start_date')->limit(5)->get();
        
        return $this->apiSuccessResponse(["events" => $events]);
    } // end of getEvents
}
