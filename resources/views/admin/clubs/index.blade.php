@extends('layouts.app')
@section('title', 'Clubs (admin)')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <h1 class="text-3xl font-bold">Clubs</h1>
    <a href="{{ route('admin.clubs.create') }}" class="bg-indigo-600 text-white rounded px-4 py-2 hover:bg-indigo-700">New club</a>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead class="bg-slate-100 text-slate-600">
            <tr>
                <th class="px-4 py-3">Name</th>
                <th class="px-4 py-3">Acronym</th>
                <th class="px-4 py-3">Email</th>
                <th class="px-4 py-3">Events</th>
                <th class="px-4 py-3">Members</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($clubs as $club)
                <tr>
                    <td class="px-4 py-3 font-medium">{{ $club->name }}</td>
                    <td class="px-4 py-3">{{ $club->acronym }}</td>
                    <td class="px-4 py-3">{{ $club->email }}</td>
                    <td class="px-4 py-3">{{ $club->events_count }}</td>
                    <td class="px-4 py-3">{{ $club->members_count }}</td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="{{ route('admin.clubs.edit', $club) }}" class="text-indigo-600 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.clubs.destroy', $club) }}" class="inline" onsubmit="return confirm('Delete this club and all its events?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline ml-3">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<p class="text-sm text-slate-500 mt-4">To make someone the manager of a club, go to <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:underline">Users</a>.</p>
@endsection
