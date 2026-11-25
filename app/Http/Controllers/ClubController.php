<?php

namespace App\Http\Controllers;

use App\Models\Club;

class ClubController extends Controller
{
    public function index()
    {
        $clubs = Club::withCount(['members', 'events'])->orderBy('name')->get();

        return view('clubs.index', compact('clubs'));
    }

    public function show(Club $club)
    {
        $club->loadCount('members');

        $events = $club->events()
            ->with(['category', 'club'])
            ->withCount('registrations')
            ->upcoming()
            ->orderBy('start_date')
            ->get();

        $isMember = auth()->check()
            && $club->members()->whereKey(auth()->id())->exists();

        return view('clubs.show', compact('club', 'events', 'isMember'));
    }
}
