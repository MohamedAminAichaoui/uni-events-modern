@extends('layouts.app')
@section('title', 'Categories')

@section('content')
<h1 class="text-3xl font-bold mb-6">Categories</h1>

<form method="POST" action="{{ route('admin.categories.store') }}" class="bg-white rounded-lg shadow-sm p-4 mb-6 flex gap-3">
    @csrf
    <input type="text" name="name" placeholder="New category name" required class="flex-1 border rounded px-3 py-2">
    <button class="bg-indigo-600 text-white rounded px-4 py-2 hover:bg-indigo-700">Add</button>
</form>

<div class="bg-white rounded-lg shadow-sm divide-y">
    @foreach($categories as $category)
        <div class="p-4 flex flex-wrap items-center gap-3">
            <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="flex flex-1 gap-3">
                @csrf @method('PUT')
                <input type="text" name="name" value="{{ $category->name }}" required class="flex-1 border rounded px-3 py-2">
                <button class="border rounded px-3 py-2 hover:bg-slate-50">Rename</button>
            </form>
            <span class="text-sm text-slate-500 w-20">{{ $category->events_count }} events</span>
            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">
                @csrf @method('DELETE')
                <button class="text-red-600 hover:underline">Delete</button>
            </form>
        </div>
    @endforeach
</div>
@endsection
