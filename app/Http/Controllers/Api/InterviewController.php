<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function index()
    {
        return Interview::paginate();
    }

    public function requestInterview(Request $request)
    {
        $interview = Interview::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'requested'
        ]);

        return response()->json($interview, 201);
    }

    public function myRequests(Request $request)
    {
        return $request->user()->interviews;
    }

    public function joinInterview(Request $request, $id)
    {
        $interview = Interview::findOrFail($id);
        // Check if user is allowed to join

        $interview->participants()->attach($request->user()->id);
        return response()->json(['message' => 'Joined interview']);
    }

    public function leaveInterview(Request $request, $id)
    {
        $interview = Interview::findOrFail($id);
        $interview->participants()->detach($request->user()->id);
        return response()->json(['message' => 'Left interview']);
    }

    public function participants($id)
    {
        $interview = Interview::with('participants')->findOrFail($id);
        return $interview->participants;
    }

    public function addToCalendar(Request $request, $id)
    {
        // This would generate a calendar event and return a .ics file or add to Google Calendar
        return response()->json(['message' => 'Added to calendar']);
    }
}
