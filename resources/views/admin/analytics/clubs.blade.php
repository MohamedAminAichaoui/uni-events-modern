@extends('layouts.app')
@section('title', 'Clubs dashboard')

@section('content')
<div class="mb-8">
    <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">Administration · Analytics</p>
    <h1 class="mt-1 text-3xl font-extrabold text-navy">Clubs dashboard</h1>
    <p class="mt-2 text-slate-500">Compare club memberships and participant activity at club-organised events.</p>
</div>

<section aria-label="Club overview" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <article class="card p-5">
        <p class="text-sm font-semibold text-slate-500">Total clubs</p>
        <p class="mt-3 text-3xl font-extrabold text-navy">{{ $clubCount }}</p>
    </article>
    <article class="card p-5">
        <p class="text-sm font-semibold text-slate-500">Club memberships</p>
        <p class="mt-3 text-3xl font-extrabold text-navy">{{ $clubMemberCount }}</p>
        <p class="mt-1 text-sm text-slate-500">Memberships across all clubs</p>
    </article>
    <article class="card p-5">
        <p class="text-sm font-semibold text-slate-500">Registrations at club events</p>
        <p class="mt-3 text-3xl font-extrabold text-navy">{{ $clubEventRegistrationCount }}</p>
        <p class="mt-1 text-sm text-slate-500">Across all club-organised events</p>
    </article>
    <article class="card p-5">
        <p class="text-sm font-semibold text-slate-500">Largest club</p>
        <p class="mt-3 truncate text-lg font-extrabold text-navy">{{ $topClub?->name ?? 'No clubs yet' }}</p>
        <p class="mt-1 text-sm text-slate-500">{{ $topClub?->members_count ?? 0 }} members</p>
    </article>
</section>

@include('admin.analytics._yearly-chart', [
    'chartTitle' => 'Monthly club growth and event participation',
    'chartSummary' => 'Compare new club memberships with registrations at club events in '.$year.'.',
    'chartDescription' => 'The gold line counts new club memberships; the navy line counts registrations at club-organised events. Each point represents one month.',
])

<section class="card mt-8 p-6">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-navy">Clubs ranked by membership</h2>
        <p class="mt-1 text-sm text-slate-500">Bars compare current member counts. Event registrations are shown as an additional measure of activity.</p>
    </div>

    @forelse($clubs as $club)
        @php($barWidth = min(100, (int) round($club->members_count / $maxClubMembers * 100)))
        @php($eventRegistrations = $club->events->sum('registrations_count'))
        <div class="{{ $loop->first ? '' : 'mt-6' }}">
            <div class="mb-2 flex items-center justify-between gap-4 text-sm">
                <div class="min-w-0">
                    <p class="truncate font-bold text-slate-800">{{ $loop->iteration }}. {{ $club->name }} <span class="font-medium text-slate-500">({{ $club->acronym }})</span></p>
                    <p class="text-xs text-slate-500">{{ $club->events_count }} {{ \Illuminate\Support\Str::plural('event', $club->events_count) }} · {{ $eventRegistrations }} registrations at club events</p>
                </div>
                <p class="shrink-0 font-bold text-navy">{{ $club->members_count }} <span class="font-medium text-slate-500">members</span></p>
            </div>
            <div class="h-3 overflow-hidden rounded-full bg-slate-100" role="meter" aria-label="Members in {{ $club->name }}" aria-valuemin="0" aria-valuemax="{{ $maxClubMembers }}" aria-valuenow="{{ $club->members_count }}">
                <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-gold-500" style="width: {{ $barWidth }}%"></div>
            </div>
        </div>
    @empty
        <p class="rounded-xl bg-slate-50 p-5 text-sm text-slate-500">No clubs to display yet.</p>
    @endforelse
</section>

<p class="mt-4 text-sm text-slate-500">Clubs are ranked by membership count. Event registrations are registrations, not unique people across multiple events.</p>
@endsection
