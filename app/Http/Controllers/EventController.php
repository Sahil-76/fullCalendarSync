<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use GuzzleHttp\Promise\Create;
use App\Http\Services\EventService;
use App\Http\Requests\CreateEventRequest;
use App\Http\Requests\UpdateEventRequest;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('events.list4');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function  refetchEvents(Request $request)
    {
        $eventService = new EventService(auth()->user());
        $eventsData = $eventService->allEvents($request->all());
        return response()->json($eventsData);
    }
    public function store(CreateEventRequest $request)
    {
        $data = $request->all();
        $data['user_id'] = auth()->user()->id;

        $eventService = new EventService($data);

        $event = $eventService->create($data);

        if ($event) {
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, string $id)
    {

        $data = $request->all();
        $eventService = new EventService(auth()->user());
        $event = $eventService->update($id, $data);
        if ($event) {
            return response()->json([
                'success' => true
            ]);
        } else {
            return response()->json([
                'success' => false
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $event = Event::find($id);
        $event->delete();

        return response()->json(['message' => 'Event deleted successfully']);
    }
}
