@extends('layouts.app')

@section('title', 'Masuk | InternHub')
@section('hide_chrome', '1')
@section('body_class', 'internhub-shell text-content antialiased')
@section('content_container_class', 'min-h-screen flex items-center justify-center px-4 py-12')

@section('content')
    <div class="w-full max-w-6xl overflow-hidden rounded-[2rem] border border-line bg-surface shadow-xl">
        <div class="grid lg:grid-cols-2">
            <section class="px-6 py-8 sm:px-10 sm:py-10 lg:px-12 lg:py-12">
                <div class="mx-auto w-full max-w-md">
                    <img src="{{ asset('logo-internhub.png') }}" alt="Logo InternHub" class="h-10 w-auto object-contain">
                    <h1 class="mt-6 text-4xl font-extrabold leading-tight tracking-tight text-content">Selamat datang kembali!</h1>
                    <p class="mt-3 text-sm text-content-muted">Masuk ke portal manajemen magang Anda untuk melanjutkan aktivitas harian.</p>
        </div>

                    <x-card class="internhub-glass internhub-shadow mt-7" title="Masuk Akun" subtitle="Silakan isi data login Anda.">
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
            </section>

            <section class="hidden bg-[#eef3ee] p-8 lg:block xl:p-10">
                <div class="h-full rounded-3xl border border-[#dfe8df] bg-[#edf3ed] p-5">
                    <img src="{{ asset('illustrasi.png') }}" alt="Ilustrasi InternHub" class="h-full w-full rounded-2xl object-cover">
                </div>
            </section>
        </div>
    </div>
@endsection
