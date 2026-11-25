<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', [
            'users' => User::with('club')->orderBy('name')->paginate(15),
            'clubs' => Club::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required', 'in:'.implode(',', [User::ROLE_ADMIN, User::ROLE_CLUB_MANAGER, User::ROLE_STUDENT])],
            'club_id' => ['nullable', 'required_if:role,'.User::ROLE_CLUB_MANAGER, 'exists:clubs,id'],
        ]);

        if ($user->id === auth()->id() && $data['role'] !== User::ROLE_ADMIN) {
            return back()->with('error', 'You cannot remove your own admin role.');
        }

        $user->update([
            'role' => $data['role'],
            'club_id' => $data['role'] === User::ROLE_CLUB_MANAGER ? $data['club_id'] : null,
        ]);

        return back()->with('success', $user->name.' updated.');
    }
}
