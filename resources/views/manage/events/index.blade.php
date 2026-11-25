@extends('layouts.app')
@section('title', 'Manage events')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <h1 class="text-3xl font-bold">
        Manage events
        @if(auth()->user()->isClubManager())
            <span class="text-lg font-normal text-slate-500">&middot; {{ auth()->user()->club?->name ?? 'no club assigned' }}</span>
        @endif
    </h1>
    <a href="{{ route('manage.events.create') }}" class="bg-indigo-600 text-white rounded px-4 py-2 hover:bg-indigo-700">New event</a>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead class="bg-slate-100 text-slate-600">
            <tr>
                <th class="px-4 py-3">Title</th>
                <th class="px-4 py-3">Organiser</th>
                <th class="px-4 py-3">Category</th>
                <th class="px-4 py-3">Date</th>
                <th class="px-4 py-3">Registered</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($events as $event)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $event->title }}</td>
                    <td class="px-4 py-3">{{ $event->organizerName() }}</td>
                    <td class="px-4 py-3">{{ $event->category->name }}</td>
                    <td class="px-4 py-3">{{ $event->start_date->format('M j Y · H:i') }}</td>
                    <td class="px-4 py-3">{{ $event->registrations_count }} / {{ $event->capacity }}</td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="{{ route('manage.events.show', $event) }}" class="text-indigo-600 hover:underline">Attendees</a>
                        <a href="{{ route('manage.events.edit', $event) }}" class="text-indigo-600 hover:underline ml-3">Edit</a>
                        <form method="POST" action="{{ route('manage.events.destroy', $event) }}" class="inline" onsubmit="return confirm('Delete this event and its registrations?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline ml-3">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-6 text-center text-slate-500">No events yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $events->links() }}</div>
@endsection
