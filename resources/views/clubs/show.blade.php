@extends('layouts.app')
@section('title', $club->name)

@section('hero')
<section class="hero-bg text-white">
    <div class="max-w-6xl mx-auto px-4 py-10">
        <a href="{{ route('clubs.index') }}" class="text-sm text-slate-300 underline hover:text-white">Back to all clubs</a>
        <div class="mt-6 flex flex-wrap items-center gap-6">
            @if($club->logo_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($club->logo_path) }}" alt="Logo for {{ $club->name }}" class="shrink-0 w-24 h-24 rounded-xl bg-white object-contain">
            @else
                <div class="shrink-0 w-24 h-24 bg-white text-navy border-b-4 border-gold-500 flex items-center justify-center px-1 text-center font-extrabold text-sm font-semibold">{{ $club->acronym }}</div>
            @endif
            <div class="flex-1 min-w-[16rem]">
                <h1 class="font-extrabold text-4xl sm:text-5xl font-extrabold leading-tight">{{ $club->name }}</h1>
                <p class="mt-2 text-slate-200">{{ $club->members_count }} members</p>
            </div>
            <div>
                @guest
                    <a href="{{ route('login') }}" class="inline-block bg-gold-500 text-navy font-semibold px-5 py-2.5 hover:bg-gold-400">Log in to join</a>
                @else
                    @if($isMember)
                        <form method="POST" action="{{ route('clubs.leave', $club) }}">
                            @csrf @method('DELETE')
                            <button class="border border-white text-white font-medium px-5 py-2.5 hover:bg-white hover:text-navy">Leave club</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('clubs.join', $club) }}">
                            @csrf
                            <button class="bg-gold-500 text-navy font-semibold px-5 py-2.5 hover:bg-gold-400">Join club</button>
                        </form>
                    @endif
                @endguest
            </div>
        </div>
    </div>
</section>
@endsection

@section('content')
<div class="max-w-prose">
    <h2 class="font-extrabold text-2xl font-semibold text-navy">About the club</h2>
    <p class="mt-4 text-lg leading-relaxed">{{ $club->description }}</p>
    @if($club->email)
        <p class="mt-4 text-slate-600">Contact: <a href="mailto:{{ $club->email }}" class="text-navy underline">{{ $club->email }}</a></p>
    @endif
</div>

<h2 class="font-extrabold text-2xl font-semibold text-navy border-b-2 border-navy pb-2 mt-12">Upcoming events</h2>
@forelse($events as $event)
    @include('partials.event-row', ['event' => $event])
@empty
    <p class="mt-4 text-slate-600">This club has no upcoming events yet.</p>
@endforelse
@endsection
