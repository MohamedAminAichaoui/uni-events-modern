@extends('layouts.app')
@section('title', $event->title)

@section('hero')
<section class="hero-bg text-white">
    <div class="max-w-6xl mx-auto px-4 py-10">
        <a href="{{ route('home') }}" class="text-sm text-slate-300 underline hover:text-white">Back to all events</a>
        <p class="mt-6">
            <span class="inline-block border border-gold-400 text-gold-400 text-sm px-2 py-0.5">{{ $event->category->name }}</span>
        </p>
        <h1 class="font-extrabold text-4xl sm:text-5xl font-extrabold leading-tight mt-3 max-w-3xl">{{ $event->title }}</h1>
        <p class="mt-3 text-slate-200">
            Organised by
            @if($event->club)
                <a href="{{ route('clubs.show', $event->club) }}" class="underline hover:text-white">{{ $event->club->name }}</a>
            @else
                the university administration
            @endif
        </p>
    </div>
</section>
@endsection

@section('content')
@php($taken = min(100, (int) round($event->registrations_count / max(1, $event->capacity) * 100)))
<div class="grid gap-12 lg:grid-cols-3">
    <div class="lg:col-span-2">
        <h2 class="font-extrabold text-2xl font-semibold text-navy">About this event</h2>
        <p class="mt-4 text-lg leading-relaxed whitespace-pre-line max-w-prose">{{ $event->description }}</p>
    </div>

    <aside class="card bg-white p-6 shadow-sm self-start">
        <dl class="space-y-4">
            <div>
                <dt class="text-sm text-slate-500">Starts</dt>
                <dd class="font-medium">{{ $event->start_date->format('l j F Y, H:i') }}</dd>
            </div>
            <div>
                <dt class="text-sm text-slate-500">Ends</dt>
                <dd class="font-medium">{{ $event->end_date->format('l j F Y, H:i') }}</dd>
            </div>
            <div>
                <dt class="text-sm text-slate-500">Location</dt>
                <dd class="font-medium">{{ $event->location }}</dd>
            </div>
            <div>
                <dt class="text-sm text-slate-500">Places</dt>
                <dd class="font-medium">{{ $event->registrations_count }} of {{ $event->capacity }} taken</dd>
                <div class="h-2 rounded-full overflow-hidden bg-slate-200 mt-2" role="presentation"><div class="h-2 rounded-full bg-gradient-to-r from-navy to-gold-500" style="width: {{ $taken }}%"></div></div>
            </div>
        </dl>

        <div class="mt-6 space-y-3">
            @guest
                <a href="{{ route('login') }}" class="block text-center bg-navy text-white font-medium px-4 py-2.5 hover:bg-navy-800">Log in to register</a>
            @else
                @if($event->isPast())
                    <p class="text-slate-600">This event has ended.</p>
                @elseif($isRegistered)
                    <p class="font-medium text-emerald-700">You are registered for this event.</p>
                    <form method="POST" action="{{ route('registrations.destroy', $event) }}">
                        @csrf @method('DELETE')
                        <button class="w-full border border-red-700 text-red-700 font-medium px-4 py-2.5 hover:bg-red-50">Cancel my registration</button>
                    </form>
                @elseif($event->isFull())
                    <p class="font-medium text-red-700">This event is full.</p>
                @else
                    <form method="POST" action="{{ route('registrations.store', $event) }}">
                        @csrf
                        <button class="w-full bg-navy text-white font-medium px-4 py-2.5 hover:bg-navy-800">Register</button>
                    </form>
                @endif
            @endguest
        </div>
    </aside>
</div>
@endsection
