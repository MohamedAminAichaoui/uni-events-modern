@extends('layouts.app')
@section('title', 'Edit event')

@section('content')
<h1 class="text-3xl font-bold mb-6">Edit event</h1>
<form method="POST" action="{{ route('manage.events.update', $event) }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow-sm p-6 space-y-6">
    @csrf @method('PUT')
    @include('manage.events._form')
    <div class="flex gap-3">
        <button class="bg-indigo-600 text-white rounded px-4 py-2 hover:bg-indigo-700">Save changes</button>
        <a href="{{ route('manage.events.index') }}" class="px-4 py-2 text-slate-600 hover:underline">Cancel</a>
    </div>
</form>
@endsection
