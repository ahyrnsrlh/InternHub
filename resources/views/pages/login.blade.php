@extends('layouts.app')

@section('title', 'Login | InternHub')
@section('hide_chrome', '1')
@section('body_class', 'internhub-shell text-content antialiased')
@section('content_container_class', 'min-h-screen flex items-center justify-center px-4 py-12')

@section('content')
<div class="w-full max-w-md">
    <div class="text-center mb-10">
        <div class="mx-auto h-14 w-14 rounded-xl bg-primary text-content-inverse grid place-content-center text-xl font-black">A</div>
        <h1 class="text-2xl font-black mt-4">The Internship</h1>
        <p class="text-xs uppercase tracking-[0.2em] text-content-muted mt-1">Executive Experience</p>
    </div>

    <x-card class="internhub-glass internhub-shadow" title="Welcome back" subtitle="Access your executive portal below.">
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            <label class="block text-xs font-bold uppercase tracking-widest text-content-muted space-y-2">
                Work Email
                <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="w-full rounded-lg border-line bg-surface/80 text-sm" placeholder="name@company.com">
            </label>

            <label class="block text-xs font-bold uppercase tracking-widest text-content-muted space-y-2">
                Password
                <input type="password" name="password" required autocomplete="current-password" class="w-full rounded-lg border-line bg-surface/80 text-sm" placeholder="••••••••">
            </label>

            <label class="inline-flex items-center gap-2 text-sm text-content-muted">
                <input type="checkbox" name="remember" class="rounded border-line-strong text-content">
                <span>Remember this device</span>
            </label>

            @if ($errors->any())
                <p class="text-sm text-danger">{{ $errors->first() }}</p>
            @endif

            <x-button type="submit" class="w-full">Sign In</x-button>
        </form>

        <div class="mt-6 pt-6 border-t border-line text-center">
            <p class="text-sm text-content-muted">New to the platform?</p>
            <a href="{{ route('register') }}" class="inline-flex mt-3 rounded-lg bg-primary-soft px-5 py-2.5 text-sm font-semibold text-content hover:bg-line-strong">Request Registration</a>
        </div>
    </x-card>

    <p class="text-center text-xs text-content-muted mt-8">&copy; {{ date('Y') }} The Internship Executive Portal.</p>
</div>
@endsection
