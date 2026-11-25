<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Club;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

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
        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('event-posters', 'public');
            if ($posterPath === false) {
                throw new RuntimeException('The event poster could not be stored.');
            }
            $data['poster_path'] = $posterPath;
        }

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

        $oldPosterPath = $event->poster_path;
        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('event-posters', 'public');
            if ($posterPath === false) {
                throw new RuntimeException('The event poster could not be stored.');
            }
            $data['poster_path'] = $posterPath;
        }

        $event->update($data);
        if (isset($data['poster_path']) && $oldPosterPath && $oldPosterPath !== $data['poster_path']) {
            if (! Storage::disk('public')->delete($oldPosterPath)) {
                throw new RuntimeException('The previous event poster could not be deleted.');
            }
        }

        return redirect()->route('manage.events.index')->with('success', 'Event updated.');
    }

    public function destroy(Event $event)
    {
        $this->authorizeEvent($event);

        $posterPath = $event->poster_path;
        $event->delete();
        if ($posterPath && ! Storage::disk('public')->delete($posterPath)) {
            throw new RuntimeException('The event poster could not be deleted.');
        }

        return redirect()->route('manage.events.index')->with('success', 'Event deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'location' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'category_id' => ['required', 'exists:categories,id'],
            'club_id' => ['nullable', 'exists:clubs,id'],
            'poster' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
        unset($data['poster']);

        return $data;
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
