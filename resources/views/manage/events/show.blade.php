@extends('layouts.app')
@section('title', 'Attendees')

@section('content')
<a href="{{ route('manage.events.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Manage events</a>

<h1 class="text-3xl font-bold mt-3">{{ $event->title }}</h1>
<p class="text-slate-500 mb-6">
    {{ $event->organizerName() }} &middot; {{ $event->start_date->format('D, M j Y · H:i') }} &middot; {{ $event->location }}
    &middot; {{ $event->registrations_count }} / {{ $event->capacity }} registered
</p>

<div class="bg-white rounded-lg shadow-sm overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead class="bg-slate-100 text-slate-600">
            <tr>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Registered on</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($event->attendees as $attendee)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $attendee->name }}</td>
                    <td class="px-4 py-3">{{ $attendee->email }}</td>
                    <td class="px-4 py-3">{{ $attendee->pivot->created_at->format('M j Y · H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-4 py-6 text-center text-slate-500">Nobody has registered yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
