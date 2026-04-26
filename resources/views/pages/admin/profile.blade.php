@extends('layouts.admin')

@section('title', 'Profil Admin')
@section('header', 'Pengaturan Profil')

@section('content')
<div class="space-y-6">
    <x-card>
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Informasi Akun Admin</h3>
                <p class="mt-1 text-sm text-gray-500">Perbarui nama dan email akun yang digunakan untuk mengelola sistem.</p>
            </div>
            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">Akun Admin</span>
        </div>

        <form id="send-verification" method="POST" action="{{ route('verification.send') }}" class="hidden">
            @csrf
        </form>

        <form method="POST" action="{{ route('internhub.admin.profile.update') }}" class="mt-6 grid gap-4 sm:max-w-2xl">
            @csrf
            @method('PATCH')

            <div>
                <label for="name" class="mb-1 block text-sm font-medium text-gray-700">Nama</label>
                <x-input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                @error('name')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                <x-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
                @error('email')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800">
                        Email belum terverifikasi.
                        <button form="send-verification" type="submit" class="ml-1 font-semibold underline">Kirim ulang verifikasi</button>
                    </div>
                @endif
            </div>

            <div class="pt-2">
                <x-button type="submit">Simpan Perubahan</x-button>
            </div>
        </form>
    </x-card>

    <x-card>
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Keamanan Akun</h3>
            <p class="mt-1 text-sm text-gray-500">Ganti kata sandi admin secara berkala untuk menjaga keamanan akun.</p>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="grid gap-4 sm:max-w-2xl">
            @csrf
            @method('PUT')

            <div>
                <label for="update_password_current_password" class="mb-1 block text-sm font-medium text-gray-700">Kata Sandi Saat Ini</label>
                <x-input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" />
                @if ($errors->updatePassword->has('current_password'))
                    <p class="mt-1 text-xs text-rose-600">{{ $errors->updatePassword->first('current_password') }}</p>
                @endif
            </div>

            <div>
                <label for="update_password_password" class="mb-1 block text-sm font-medium text-gray-700">Kata Sandi Baru</label>
                <x-input id="update_password_password" name="password" type="password" autocomplete="new-password" />
                @if ($errors->updatePassword->has('password'))
                    <p class="mt-1 text-xs text-rose-600">{{ $errors->updatePassword->first('password') }}</p>
                @endif
            </div>

            <div>
                <label for="update_password_password_confirmation" class="mb-1 block text-sm font-medium text-gray-700">Konfirmasi Kata Sandi Baru</label>
                <x-input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
                @if ($errors->updatePassword->has('password_confirmation'))
                    <p class="mt-1 text-xs text-rose-600">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                @endif
            </div>

            <div class="pt-2">
                <x-button type="submit">Perbarui Kata Sandi</x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
