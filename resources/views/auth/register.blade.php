@extends('layouts.app')

@section('title', 'Register | InternHub')
@section('hide_chrome', '1')
@section('body_class', 'internhub-shell text-content antialiased')
@section('content_container_class', 'min-h-screen flex items-center justify-center px-4 py-12')

@section('content')
    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <div class="mx-auto h-14 w-14 rounded-xl bg-primary text-content-inverse grid place-content-center text-xl font-black">A</div>
            <h1 class="text-2xl font-black mt-4">Join InternHub</h1>
            <p class="text-xs uppercase tracking-[0.2em] text-content-muted mt-1">Executive Experience</p>
        </div>

        <x-card class="internhub-glass internhub-shadow" title="Request Registration" subtitle="Create your executive internship account.">
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <label class="block text-xs font-bold uppercase tracking-widest text-content-muted space-y-2">
                    Full Name
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="w-full rounded-lg border-line bg-surface/80 text-sm" placeholder="Alex Carter">
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </label>

                <label class="block text-xs font-bold uppercase tracking-widest text-content-muted space-y-2">
                    Work Email
                    <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="w-full rounded-lg border-line bg-surface/80 text-sm" placeholder="name@company.com">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </label>

                <label class="block text-xs font-bold uppercase tracking-widest text-content-muted space-y-2">
                    Password
                    <input type="password" name="password" required autocomplete="new-password" class="w-full rounded-lg border-line bg-surface/80 text-sm" placeholder="••••••••">
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </label>

                <label class="block text-xs font-bold uppercase tracking-widest text-content-muted space-y-2">
                    Confirm Password
                    <input type="password" name="password_confirmation" required autocomplete="new-password" class="w-full rounded-lg border-line bg-surface/80 text-sm" placeholder="••••••••">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </label>

                <x-button type="submit" class="w-full">Create Account</x-button>
            </form>

            <p class="mt-6 text-center text-sm text-content-muted">
                Already registered?
                <a href="{{ route('login') }}" class="font-semibold text-content hover:underline">Sign in here</a>
            </p>
        </x-card>
    </div>
@endsection
