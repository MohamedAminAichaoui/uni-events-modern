@extends('layouts.app')
@section('title', 'Clubs')

@section('hero')
<section class="hero-bg text-white">
    <div class="max-w-6xl mx-auto px-4 py-16">
        <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight">Clubs and <span class="text-gold-400">societies</span></h1>
        <p class="mt-4 text-lg text-slate-300 max-w-xl">Join a club to follow what it organises, from technical workshops to community projects.</p>
    </div>
</section>
@endsection

@section('content')
<div class="grid gap-5 md:grid-cols-2">
    @foreach($clubs as $club)
        <a href="{{ route('clubs.show', $club) }}" class="card reveal group flex gap-5 p-6">
            @if($club->logo_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($club->logo_path) }}" alt="Logo for {{ $club->name }}" class="shrink-0 w-20 h-20 rounded-2xl object-contain bg-white shadow-lg group-hover:scale-105 transition">
            @else
                <div class="shrink-0 w-20 h-20 rounded-2xl bg-gradient-to-br from-navy to-indigo-500 flex items-center justify-center px-1 text-center text-sm font-extrabold text-gold-400 shadow-lg shadow-navy/20 group-hover:rotate-3 group-hover:scale-105 transition">{{ $club->acronym }}</div>
            @endif
            <div class="min-w-0">
                <h2 class="text-xl font-bold text-navy group-hover:text-indigo-500 transition">{{ $club->name }}</h2>
                <p class="mt-1 text-slate-600 text-sm">{{ \Illuminate\Support\Str::limit($club->description, 110) }}</p>
                <p class="mt-3 text-xs font-semibold"><span class="bg-slate-100 rounded-full px-3 py-1">{{ $club->members_count }} members</span> <span class="bg-gold-400/20 rounded-full px-3 py-1">{{ $club->events_count }} events</span></p>
            </div>
        </a>
    @endforeach
</div>
@endsection
