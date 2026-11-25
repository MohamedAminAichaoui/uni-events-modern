<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use RuntimeException;

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
        $data = $this->validated($request);
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('club-logos', 'public');
            if ($logoPath === false) {
                throw new RuntimeException('The club logo could not be stored.');
            }
            $data['logo_path'] = $logoPath;
        }

        Club::create($data);

        return redirect()->route('admin.clubs.index')->with('success', 'Club created.');
    }

    public function edit(Club $club)
    {
        return view('admin.clubs.edit', compact('club'));
    }

    public function update(Request $request, Club $club)
    {
        $data = $this->validated($request, $club);
        $oldLogoPath = $club->logo_path;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('club-logos', 'public');
            if ($logoPath === false) {
                throw new RuntimeException('The club logo could not be stored.');
            }
            $data['logo_path'] = $logoPath;
        }

        $club->update($data);
        if (isset($data['logo_path']) && $oldLogoPath && $oldLogoPath !== $data['logo_path']) {
            if (! Storage::disk('public')->delete($oldLogoPath)) {
                throw new RuntimeException('The previous club logo could not be deleted.');
            }
        }

        return redirect()->route('admin.clubs.index')->with('success', 'Club updated.');
    }

    public function destroy(Club $club)
    {
        $logoPath = $club->logo_path;
        $club->delete(); // its events and memberships are deleted too (cascade)
        if ($logoPath && ! Storage::disk('public')->delete($logoPath)) {
            throw new RuntimeException('The club logo could not be deleted.');
        }

        return redirect()->route('admin.clubs.index')->with('success', 'Club deleted.');
    }

    private function validated(Request $request, ?Club $club = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('clubs', 'name')->ignore($club)],
            'acronym' => ['required', 'string', 'max:20'],
            'description' => ['required', 'string'],
            'email' => ['nullable', 'email', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        unset($data['logo']);

        return $data;
    }
}
