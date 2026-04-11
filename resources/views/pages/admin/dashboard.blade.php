@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('header', 'Admin Dashboard')

@section('content')
<div class="space-y-6">
    <x-card>
        <h3 class="text-lg font-semibold text-gray-900">Role-Based Access Initialized</h3>
        <p class="mt-2 text-sm text-gray-600">Area admin aktif. Gunakan halaman ini sebagai titik awal untuk manajemen user, monitoring, dan approval.</p>
    </x-card>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <x-card>
            <p class="text-sm text-gray-500">Total Intern</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">0</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Pending Review</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">0</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Attendance Today</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">0</p>
        </x-card>
    </div>
</div>
@endsection
