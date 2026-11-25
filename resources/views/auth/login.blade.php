@extends('layouts.app')
@section('title', 'Log in')

@section('content')
<div class="max-w-md mx-auto border-t-4 border-navy bg-slate-50 p-8">
    <h1 class="font-extrabold text-3xl font-semibold text-navy">Log in</h1>
    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1" for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full border border-slate-300 bg-white px-3 py-2.5">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" for="password">Password</label>
            <input id="password" type="password" name="password" required class="w-full border border-slate-300 bg-white px-3 py-2.5">
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="remember"> Keep me logged in
        </label>
        <button class="w-full bg-navy text-white font-medium px-4 py-2.5 hover:bg-navy-800">Log in</button>
    </form>
    <p class="mt-6 text-sm text-slate-600">No account yet? <a href="{{ route('register') }}" class="text-navy font-medium underline">Create a student account</a></p>
</div>
@endsection
