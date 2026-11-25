@extends('layouts.app')
@section('title', 'Sign up')

@section('content')
<div class="max-w-md mx-auto border-t-4 border-navy bg-slate-50 p-8">
    <h1 class="font-extrabold text-3xl font-semibold text-navy">Create a student account</h1>
    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-1" for="name">Full name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="w-full border border-slate-300 bg-white px-3 py-2.5">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="w-full border border-slate-300 bg-white px-3 py-2.5">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" for="password">Password (8 characters or more)</label>
            <input id="password" type="password" name="password" required class="w-full border border-slate-300 bg-white px-3 py-2.5">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required class="w-full border border-slate-300 bg-white px-3 py-2.5">
        </div>
        <button class="w-full bg-navy text-white font-medium px-4 py-2.5 hover:bg-navy-800">Create account</button>
    </form>
    <p class="mt-6 text-sm text-slate-600">Already registered? <a href="{{ route('login') }}" class="text-navy font-medium underline">Log in</a></p>
</div>
@endsection
