<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Club;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::with(['category', 'club'])
            ->withCount('registrations')
            ->upcoming()
            ->when($request->filled('q'), fn ($query) => $query->where('title', 'like', '%'.$request->q.'%'))
            ->when($request->filled('category'), fn ($query) => $query->where('category_id', $request->category))
            ->when($request->filled('club'), function ($query) use ($request) {
                return $request->club === 'university'
                    ? $query->whereNull('club_id')
                    : $query->where('club_id', $request->club);
            })
            ->orderBy('start_date')
            ->paginate(10)
            ->withQueryString();

        $featured = Event::with(['category', 'club'])
            ->withCount('registrations')
            ->upcoming()
            ->orderBy('start_date')
            ->first();

        return view('events.index', [
            'events' => $events,
            'featured' => $featured,
            'categories' => Category::orderBy('name')->get(),
            'clubs' => Club::orderBy('name')->get(),
        ]);
    }

    public function show(Event $event)
    {
        $event->load(['category', 'club'])->loadCount('registrations');

        $isRegistered = auth()->check()
            && $event->attendees()->whereKey(auth()->id())->exists();

        return view('events.show', compact('event', 'isRegistered'));
    }
}
