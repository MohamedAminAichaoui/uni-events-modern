<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClubController extends Controller
{
    public function index()
    {
        $clubs = Club::withCount(['events', 'members'])->orderBy('name')->get();

        return view('admin.clubs.index', compact('clubs'));
    }

    public function create()
    {
        return view('admin.clubs.create', ['club' => new Club]);
    }

    public function store(Request $request)
    {
        Club::create($this->validated($request));

        return redirect()->route('admin.clubs.index')->with('success', 'Club created.');
    }

    public function edit(Club $club)
    {
        return view('admin.clubs.edit', compact('club'));
    }

    public function update(Request $request, Club $club)
    {
        $club->update($this->validated($request, $club));

        return redirect()->route('admin.clubs.index')->with('success', 'Club updated.');
    }

    public function destroy(Club $club)
    {
        $club->delete(); // its events and memberships are deleted too (cascade)

        return redirect()->route('admin.clubs.index')->with('success', 'Club deleted.');
    }

    private function validated(Request $request, ?Club $club = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('clubs', 'name')->ignore($club)],
            'acronym' => ['required', 'string', 'max:20'],
            'description' => ['required', 'string'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);
    }
}
