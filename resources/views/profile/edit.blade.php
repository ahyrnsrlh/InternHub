@extends('layouts.app')

@section('title', 'Profile Settings')
@section('active_menu', 'dashboard')
@section('nav_title', 'Profile')

@section('content')
    <section class="space-y-2">
        <h1 class="text-4xl font-black tracking-tight text-content">Profile Settings</h1>
        <p class="text-content-muted">Manage your account details and security preferences.</p>
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
