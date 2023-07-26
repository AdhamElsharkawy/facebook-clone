<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Traits\ImageTrait;
use App\Models\Event;
use App\Http\Requests\Admin\Event\StoreEventRequest;
use App\Http\Requests\Admin\Event\UpdateEventRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventController extends Controller
{
    use ImageTrait;

    public function index()
    {
        $event = Event::latest()->get();
        return ['events' => $event];
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request)
    {
        $form_data = $request->validated();
        $form_data['start_date'] = Carbon::parse($request->start_date)->format('Y-m-d H:i:s');
        $form_data['end_date'] = Carbon::parse($request->end_date)->format('Y-m-d H:i:s');
        //image uploading
        $request->image ? $form_data['image'] = $this->uploadS3Image($request->image, 'images/events') : '';

        Event::create($form_data);

        return response()->json(['message' => __('Event Created Successfully')]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        return response()->json(['event' => $event]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        //image uploading
        $form_data = $request->validated();
        if ($request->image) {
            $event->image !=  'assets/images/default.png' ? $this->deleteS3Image($event->image) : '';
            $form_data['image'] = $this->uploadS3Image($request->image, 'images/events');
        } else {
            $form_data['image'] = $event->image;
        }
        $form_data['start_date'] = Carbon::parse($request->start_date);
        $form_data['end_date'] = Carbon::parse($request->end_date);
        $event->update($form_data);

        return response()->json(['message' => __('Event Updated Successfully')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->image != 'assets/images/default.png' ? $this->deleteS3Image($event->image) : '';
        $event->delete();

        return response()->json(['message' => __('Event Deleted Successfully')], 200);
    }

    public function destroyAll(Request $request)
    {
        $events = Event::whereIn('id', $request->events)->get();
        foreach ($events as $event) {
            $event->image != 'assets/images/default.png' ? $this->deleteS3Image($event->image) : '';
            $event->delete();
        }
        return response()->json(['message' => __('Events Deleted Successfully')]);
    } //end of destroyAll
}
