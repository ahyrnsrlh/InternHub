@extends('layouts.app')

@section('title', 'Masuk | InternHub')
@section('hide_chrome', '1')
@section('body_class', 'internhub-shell text-content antialiased')
@section('content_container_class', 'min-h-screen flex items-center justify-center px-4 py-12')

@section('content')
    <div class="w-full max-w-md">
        <div class="text-center mb-10">
            <div class="mx-auto h-14 w-14 rounded-xl bg-primary text-content-inverse grid place-content-center text-xl font-black">A</div>
            <h1 class="text-2xl font-black mt-4">InternHub</h1>
            <p class="text-xs uppercase tracking-[0.2em] text-content-muted mt-1">Portal Manajemen Magang</p>
        </div>

        <x-card class="internhub-glass internhub-shadow" title="Selamat datang kembali" subtitle="Masuk ke portal manajemen Anda melalui formulir berikut.">
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <label class="block text-xs font-bold uppercase tracking-widest text-content-muted space-y-2">
                    Email Kerja
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="w-full rounded-lg border-line bg-surface/80 text-sm" placeholder="nama@instansi.com">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </label>

                <label class="block text-xs font-bold uppercase tracking-widest text-content-muted space-y-2">
                    Kata Sandi
                    <input type="password" name="password" required autocomplete="current-password" class="w-full rounded-lg border-line bg-surface/80 text-sm" placeholder="••••••••">
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </label>

                <div class="flex items-center justify-between gap-4">
                    <label class="inline-flex items-center gap-2 text-sm text-content-muted">
                        <input type="checkbox" name="remember" class="rounded border-line-strong text-content">
                        <span>Ingat perangkat ini</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-content-muted hover:underline">Lupa kata sandi?</a>
                    @endif
                </div>

                <x-button type="submit" class="w-full">Masuk</x-button>
            </form>

            <div class="mt-6 pt-6 border-t border-line text-center">
                <p class="text-sm text-content-muted">Belum memiliki akun?</p>
                <a href="{{ route('register') }}" class="inline-flex mt-3 rounded-lg bg-primary-soft px-5 py-2.5 text-sm font-semibold text-content hover:bg-line-strong">Ajukan Pendaftaran</a>
            </div>
        </x-card>
    </div>
@endsection
