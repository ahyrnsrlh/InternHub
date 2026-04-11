@extends('layouts.app')

@section('title', 'Beranda')
@section('active_menu', 'dashboard')
@section('nav_title', 'Portal Manajemen')

@section('content')
    <x-card title="Beranda Terautentikasi" subtitle="Rute bawaan starter-kit tetap berfungsi.">
        <p class="text-content-muted">Anda sudah masuk.</p>
    </x-card>
@endsection
