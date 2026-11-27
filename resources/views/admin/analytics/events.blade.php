@extends('layouts.app')
@section('title', 'Events dashboard')

@section('content')
<div class="mb-8">
    <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">Administration · Analytics</p>
    <h1 class="mt-1 text-3xl font-extrabold text-navy">Events dashboard</h1>
    <p class="mt-2 text-slate-500">Compare events by participant registrations and identify the most popular ones.</p>
</div>

<section aria-label="Event overview" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <article class="card p-5">
        <p class="text-sm font-semibold text-slate-500">Total events</p>
        <p class="mt-3 text-3xl font-extrabold text-navy">{{ $eventCount }}</p>
    </article>
    <article class="card p-5">
        <p class="text-sm font-semibold text-slate-500">Upcoming events</p>
        <p class="mt-3 text-3xl font-extrabold text-navy">{{ $upcomingEventCount }}</p>
    </article>
    <article class="card p-5">
        <p class="text-sm font-semibold text-slate-500">Total registrations</p>
        <p class="mt-3 text-3xl font-extrabold text-navy">{{ $registrationCount }}</p>
        <p class="mt-1 text-sm text-slate-500">Across all events</p>
    </article>
    <article class="card p-5">
        <p class="text-sm font-semibold text-slate-500">Most popular event</p>
        <p class="mt-3 truncate text-lg font-extrabold text-navy">{{ $topEvent?->title ?? 'No events yet' }}</p>
        <p class="mt-1 text-sm text-slate-500">{{ $topEvent?->registrations_count ?? 0 }} registrations</p>
    </article>
</section>

@include('admin.analytics._yearly-chart', [
    'chartTitle' => 'Monthly participant registrations',
    'chartSummary' => 'See how event sign-ups changed month by month in '.$year.'.',
    'chartDescription' => 'Each point counts new participant registrations recorded during that month. Rising values mean more new sign-ups.',
])

<section class="card mt-8 p-6">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-navy">Events ranked by participants</h2>
        <p class="mt-1 text-sm text-slate-500">The longest bar represents the event with the most registrations. Each count is shown against event capacity.</p>
    </div>

    @forelse($events as $event)
        @php($barWidth = min(100, (int) round($event->registrations_count / $maxRegistrations * 100)))
        <div class="{{ $loop->first ? '' : 'mt-6' }}">
            <div class="mb-2 flex items-center justify-between gap-4 text-sm">
                <div class="min-w-0">
                    <p class="truncate font-bold text-slate-800">{{ $loop->iteration }}. {{ $event->title }}</p>
                    <p class="text-xs text-slate-500">{{ $event->club?->name ?? 'University event' }} · {{ $event->start_date->format('M j, Y') }}</p>
                </div>
                <p class="shrink-0 font-bold text-navy">{{ $event->registrations_count }} <span class="font-medium text-slate-500">/ {{ $event->capacity }} participants</span></p>
            </div>
            <div class="h-3 overflow-hidden rounded-full bg-slate-100" role="meter" aria-label="Participant registrations for {{ $event->title }}" aria-valuemin="0" aria-valuemax="{{ $maxRegistrations }}" aria-valuenow="{{ $event->registrations_count }}">
                <div class="h-full rounded-full bg-gradient-to-r from-navy to-gold-500" style="width: {{ $barWidth }}%"></div>
            </div>
        </div>
    @empty
        <p class="rounded-xl bg-slate-50 p-5 text-sm text-slate-500">No events to display yet.</p>
    @endforelse
</section>

<p class="mt-4 text-sm text-slate-500">Popularity is measured by the number of registered participants, not by event capacity or date.</p>
@endsection
