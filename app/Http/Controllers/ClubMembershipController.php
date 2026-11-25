<?php

namespace App\Http\Controllers;

use App\Models\Club;

class ClubMembershipController extends Controller
{
    public function store(Club $club)
    {
        $club->members()->syncWithoutDetaching([auth()->id()]);

        return back()->with('success', 'You joined '.$club->name.'.');
    }

    public function destroy(Club $club)
    {
        $club->members()->detach(auth()->id());

        return back()->with('success', 'You left '.$club->name.'.');
    }
}
