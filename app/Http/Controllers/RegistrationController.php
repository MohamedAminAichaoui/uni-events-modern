<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    public function index()
    {
        $events = auth()->user()
            ->registeredEvents()
            ->with(['category', 'club'])
            ->orderBy('start_date')
            ->get();

        return view('registrations.index', compact('events'));
    }

    public function store(Event $event)
    {
        $user = auth()->user();

        // Lock the event row so two simultaneous requests cannot take the last seat.
        $error = DB::transaction(function () use ($event, $user) {
            $locked = Event::whereKey($event->id)->lockForUpdate()->first();

            if ($locked->isPast()) {
                return 'This event has already ended.';
            }
            if ($locked->attendees()->whereKey($user->id)->exists()) {
                return 'You are already registered for this event.';
            }
            if ($locked->isFull()) {
                return 'This event is full.';
            }

            $locked->attendees()->attach($user->id);

            return null;
        });

        return back()->with($error ? 'error' : 'success', $error ?? 'You are registered. See you there!');
    }

    public function destroy(Event $event)
    {
        $event->attendees()->detach(auth()->id());

        return back()->with('success', 'Your registration was cancelled.');
    }
}
