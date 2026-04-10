@extends('layouts.app')

@section('title', 'Dashboard')
@section('active_menu', 'dashboard')
@section('nav_title', 'Executive Portal')

@section('content')
    <x-card title="Authenticated Dashboard" subtitle="Starter-kit route remains functional.">
        <p class="text-content-muted">{{ __("You're logged in!") }}</p>
    </x-card>
@endsection
