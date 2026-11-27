@extends('layouts.app')
@section('title', 'Analytics dashboard')

@section('content')
<div class="mb-8">
    <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">Administration</p>
    <h1 class="mt-1 text-3xl font-extrabold text-navy">Analytics dashboard</h1>
    <p class="mt-2 text-slate-500">Track event registrations and club participation at a glance.</p>
</div>

<section aria-label="Overview" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <article class="card p-5">
        <p class="text-sm font-semibold text-slate-500">Total events</p>
        <p class="mt-3 text-3xl font-extrabold text-navy">{{ $eventCount }}</p>
        <p class="mt-1 text-sm text-slate-500">{{ $upcomingEventCount }} upcoming</p>
    </article>
    <article class="card p-5">
        <p class="text-sm font-semibold text-slate-500">Event registrations</p>
        <p class="mt-3 text-3xl font-extrabold text-navy">{{ $registrationCount }}</p>
        <p class="mt-1 text-sm text-slate-500">Across all events</p>
    </article>
    <article class="card p-5">
        <p class="text-sm font-semibold text-slate-500">Club members</p>
        <p class="mt-3 text-3xl font-extrabold text-navy">{{ $clubMemberCount }}</p>
        <p class="mt-1 text-sm text-slate-500">Memberships across all clubs</p>
    </article>
    <article class="card p-5">
        <p class="text-sm font-semibold text-slate-500">Total clubs</p>
        <p class="mt-3 text-3xl font-extrabold text-navy">{{ $clubCount }}</p>
        <p class="mt-1 text-sm text-slate-500">Registered clubs</p>
    </article>
</section>

<section class="mt-8 grid gap-6 lg:grid-cols-2">
    <div class="card p-6">
        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-navy">Event participation</h2>
                <p class="mt-1 text-sm text-slate-500">Latest events · bar shows capacity filled</p>
            </div>
            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">Participants</span>
        </div>

        @forelse($events as $event)
            @php($eventBarWidth = min(100, (int) round($event->registrations_count / $event->capacity * 100)))
            <div class="{{ $loop->first ? '' : 'mt-5' }}">
                <div class="mb-2 flex items-center justify-between gap-3 text-sm">
                    <div class="min-w-0">
                        <p class="truncate font-semibold text-slate-800">{{ $event->title }}</p>
                        <p class="text-xs text-slate-500">{{ $event->club?->name ?? 'University event' }} · {{ $event->start_date->format('M j, Y') }}</p>
                    </div>
                    <p class="shrink-0 font-bold text-navy">{{ $event->registrations_count }} <span class="font-medium text-slate-500">/ {{ $event->capacity }}</span></p>
                </div>
                <div class="h-2.5 overflow-hidden rounded-full bg-slate-100" role="meter" aria-label="Registrations for {{ $event->title }}" aria-valuemin="0" aria-valuemax="{{ $event->capacity }}" aria-valuenow="{{ min($event->capacity, $event->registrations_count) }}">
                    <div class="h-full rounded-full bg-gradient-to-r from-navy to-gold-500" style="width: {{ $eventBarWidth }}%"></div>
                </div>
            </div>
        @empty
            <p class="rounded-xl bg-slate-50 p-5 text-sm text-slate-500">No events to display yet.</p>
        @endforelse
    </div>

    <div class="card p-6">
        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-navy">Club participation</h2>
                <p class="mt-1 text-sm text-slate-500">Member counts and registrations at club events</p>
            </div>
            <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">Top clubs</span>
        </div>

        @forelse($clubs as $club)
            @php($clubEventRegistrations = $club->events->sum('registrations_count'))
            @php($clubBarWidth = min(100, (int) round($club->members_count / $maxClubMembers * 100)))
            <div class="{{ $loop->first ? '' : 'mt-5' }}">
                <div class="mb-2 flex items-center justify-between gap-3 text-sm">
                    <div class="min-w-0">
                        <p class="truncate font-semibold text-slate-800">{{ $club->name }}</p>
                        <p class="text-xs text-slate-500">{{ $club->events_count }} {{ \Illuminate\Support\Str::plural('event', $club->events_count) }} · {{ $clubEventRegistrations }} event {{ \Illuminate\Support\Str::plural('registration', $clubEventRegistrations) }}</p>
                    </div>
                    <p class="shrink-0 font-bold text-navy">{{ $club->members_count }} <span class="font-medium text-slate-500">members</span></p>
                </div>
                <div class="h-2.5 overflow-hidden rounded-full bg-slate-100" role="meter" aria-label="Members in {{ $club->name }}" aria-valuemin="0" aria-valuemax="{{ $maxClubMembers }}" aria-valuenow="{{ $club->members_count }}">
                    <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-gold-500" style="width: {{ $clubBarWidth }}%"></div>
                </div>
            </div>
        @empty
            <p class="rounded-xl bg-slate-50 p-5 text-sm text-slate-500">No clubs to display yet.</p>
        @endforelse
    </div>
</section>
@endsection
