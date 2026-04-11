@extends('layouts.app')

@section('title', 'Pengaturan Profil')
@section('active_menu', 'dashboard')
@section('nav_title', 'Profil')

@section('content')
    <section class="space-y-2">
        <h1 class="text-4xl font-black tracking-tight text-content">Pengaturan Profil</h1>
        <p class="text-content-muted">Kelola detail akun dan preferensi keamanan Anda.</p>
    </section>

    <section class="space-y-6">
        <x-card>
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </x-card>

        <x-card>
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </x-card>

        <x-card>
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </x-card>
    </section>
@endsection
