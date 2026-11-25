@extends('layouts.app')
@section('title', 'Users')

@section('content')
<h1 class="text-3xl font-bold mb-2">Users</h1>
<p class="text-slate-500 mb-6">Set a user's role. A club manager must be assigned to a club and can only manage that club's events.</p>

<div class="bg-white rounded-lg shadow-sm overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead class="bg-slate-100 text-slate-600">
            <tr>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Role and club</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($users as $user)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                    <td class="px-4 py-3">{{ $user->email }}</td>
                    <td class="px-4 py-3">
                        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex flex-wrap gap-2">
                            @csrf @method('PUT')
                            <select name="role" class="border rounded px-2 py-1">
                                <option value="student" @selected($user->role === 'student')>Student</option>
                                <option value="club_manager" @selected($user->role === 'club_manager')>Club manager</option>
                                <option value="admin" @selected($user->role === 'admin')>Admin</option>
                            </select>
                            <select name="club_id" class="border rounded px-2 py-1">
                                <option value="">No club</option>
                                @foreach($clubs as $club)
                                    <option value="{{ $club->id }}" @selected($user->club_id == $club->id)>{{ $club->name }}</option>
                                @endforeach
                            </select>
                            <button class="border rounded px-3 py-1 hover:bg-slate-50">Save</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $users->links() }}</div>
@endsection
