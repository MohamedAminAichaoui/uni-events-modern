@extends('layouts.app')
@section('title', 'My registrations')

@section('hero')
<section class="hero-bg text-white">
    <div class="max-w-6xl mx-auto px-4 py-10">
        <h1 class="font-extrabold text-4xl font-semibold">My registrations</h1>
        <p class="mt-3 text-slate-200">The events you signed up for, soonest first.</p>
    </div>
</section>
@endsection

@section('content')
@if($events->isEmpty())
    <p class="text-slate-600">You have not registered for any event yet. <a href="{{ route('home') }}" class="text-navy font-medium underline">Browse upcoming events</a></p>
@else
    @foreach($events as $event)
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[18rem]">
                @include('partials.event-row', ['event' => $event, 'muted' => $event->isPast()])
            </div>
            @if($event->isPast())
                <span class="text-sm text-slate-500">Finished</span>
            @else
                <form method="POST" action="{{ route('registrations.destroy', $event) }}">
                    @csrf @method('DELETE')
                    <button class="text-sm border border-red-700 text-red-700 font-medium px-3 py-2 hover:bg-red-50">Cancel registration</button>
                </form>
            @endif
        </div>
    @endforeach
@endif
@endsection
