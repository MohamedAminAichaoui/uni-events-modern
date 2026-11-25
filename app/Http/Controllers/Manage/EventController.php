<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Club;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $events = Event::with(['category', 'club'])
            ->withCount('registrations')
            ->when($user->isClubManager(), fn ($query) => $query->where('club_id', $user->club_id))
            ->latest('start_date')
            ->paginate(10);

        return view('manage.events.index', compact('events'));
    }

    public function create()
    {
        $this->ensureCanCreate();

        return view('manage.events.create', $this->formData(new Event));
    }

    public function store(Request $request)
    {
        $this->ensureCanCreate();

        $data = $this->validated($request);
        $data['club_id'] = auth()->user()->isAdmin() ? ($request->input('club_id') ?: null) : auth()->user()->club_id;
        $data['created_by'] = auth()->id();

        Event::create($data);

        return redirect()->route('manage.events.index')->with('success', 'Event created.');
    }

    public function show(Event $event)
    {
        $this->authorizeEvent($event);

        $event->load(['category', 'club', 'attendees'])->loadCount('registrations');

        return view('manage.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        $this->authorizeEvent($event);

        return view('manage.events.edit', $this->formData($event));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorizeEvent($event);

        $data = $this->validated($request);

        if (auth()->user()->isAdmin()) {
            $data['club_id'] = $request->input('club_id') ?: null;
        } else {
            unset($data['club_id']); // a club manager cannot move an event to another club
        }

        $event->update($data);

        return redirect()->route('manage.events.index')->with('success', 'Event updated.');
    }

    public function destroy(Event $event)
    {
        $this->authorizeEvent($event);

        $event->delete();

        return redirect()->route('manage.events.index')->with('success', 'Event deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'location' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'category_id' => ['required', 'exists:categories,id'],
            'club_id' => ['nullable', 'exists:clubs,id'],
        ]);
    }

    private function formData(Event $event): array
    {
        return [
            'event' => $event,
            'categories' => Category::orderBy('name')->get(),
            'clubs' => Club::orderBy('name')->get(),
        ];
    }

    private function authorizeEvent(Event $event): void
    {
        abort_unless(auth()->user()->canManage($event), 403);
    }

    private function ensureCanCreate(): void
    {
        $user = auth()->user();

        abort_if(
            $user->isClubManager() && $user->club_id === null,
            403,
            'No club is assigned to your account yet. Ask an administrator.'
        );
    }
}
