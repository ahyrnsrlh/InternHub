@extends('layouts.app')

@section('title', 'Daftar | InternHub')
@section('hide_chrome', '1')
@section('body_class', 'internhub-shell text-content antialiased')
@section('content_container_class', 'min-h-screen flex items-center justify-center px-4 py-12')

@section('content')
    <div class="w-full max-w-6xl overflow-hidden rounded-[2rem] border border-gray-200 bg-white shadow-xl">
        <div class="grid lg:grid-cols-2">
            <section class="px-6 py-8 sm:px-10 sm:py-10 lg:px-12 lg:py-12">
                <div class="mx-auto w-full max-w-md">
                    <img src="{{ asset('logo-internhub.png') }}" alt="Logo InternHub" class="h-10 w-auto object-contain">
                    <h1 class="mt-6 text-4xl font-extrabold leading-tight tracking-tight text-gray-900">Buat akun baru</h1>
                    <p class="mt-3 text-sm text-gray-500">Daftarkan akun dengan data dasar. Perekaman wajah dilakukan setelah login pada halaman profil.</p>

                    <form method="POST" action="{{ route('register') }}" class="mt-7 space-y-4">
                        @csrf

                        <div>
                            <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Contoh: Aditya Pratama" class="w-full rounded-full border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-200">
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">Email Aktif</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="nama@instansi.com" class="w-full rounded-full border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-200">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">Kata Sandi</label>
                            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Buat kata sandi" class="w-full rounded-full border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-200">
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700">Konfirmasi Kata Sandi</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi" class="w-full rounded-full border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-200">
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <button type="submit" class="w-full rounded-full bg-black px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800">Daftarkan Akun</button>
                    </form>

                    <p class="mt-7 text-center text-sm text-gray-500">Sudah memiliki akun? <a href="{{ route('internhub.login') }}" class="font-semibold text-emerald-700 hover:text-emerald-800">Masuk sekarang</a></p>
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
