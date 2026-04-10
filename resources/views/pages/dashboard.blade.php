@extends('layouts.app')

@section('title', 'Intern Dashboard')
@section('active_menu', 'dashboard')
@section('nav_title', 'Executive Portal')
@section('search_placeholder', 'Search experiences...')

@section('content')
<section class="space-y-2">
    <h1 class="text-5xl font-black tracking-tight text-content">Welcome back, Alex.</h1>
    <p class="text-content-muted text-lg max-w-2xl">Your professional trajectory is currently <span class="font-semibold text-success">Exceeding Expectations</span>. Here is your operational summary for this week.</p>
</section>

<section class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach ($metrics as $metric)
        <x-card>
            <p class="text-xs uppercase tracking-[0.15em] font-bold text-content-muted">{{ $metric['label'] }}</p>
            <p class="text-3xl font-black text-content mt-3">{{ $metric['value'] }}</p>
            <p class="text-sm text-content-muted mt-4">{{ $metric['hint'] }}</p>
        </x-card>
    @endforeach
</section>

<section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <x-card class="lg:col-span-2" title="Activity Tracking" subtitle="Weekly performance timeline">
        <div class="grid grid-cols-7 gap-2 items-end h-64">
            @php($barHeights = ['h-40', 'h-48', 'h-52', 'h-56', 'h-44', 'h-24', 'h-16'])
            @foreach ($barHeights as $index => $heightClass)
                <div class="flex flex-col items-center gap-2">
                    <div class="w-full rounded-t-md {{ $index === 3 ? 'bg-primary' : 'bg-line-strong' }} {{ $heightClass }}"></div>
                    <span class="text-[10px] font-bold text-content-muted">{{ ['MON','TUE','WED','THU','FRI','SAT','SUN'][$index] }}</span>
                </div>
            @endforeach
        </div>
    </x-card>

    <x-card title="Recent Activity">
        <div class="space-y-3">
            @forelse ($activities as $item)
                <article class="rounded-xl border border-line p-4">
                    <p class="text-sm font-bold text-content">{{ $item['title'] }}</p>
                    <p class="text-xs text-content-muted mt-1">{{ $item['detail'] }}</p>
                    <p class="text-[11px] text-content-soft mt-2 uppercase tracking-wide">{{ $item['time'] }}</p>
                </article>
            @empty
                <p class="text-sm text-content-muted">No activity yet. Seed InternHub data to populate this section.</p>
            @endforelse
        </div>
    </x-card>
</section>

<x-card class="bg-primary text-content-inverse border-primary-hover" title="Mentor Feedback">
    <blockquote class="text-2xl font-bold leading-relaxed">"Alex has shown an incredible eye for structural integrity in the latest logistics proposal."</blockquote>
    <div class="mt-6">
        <x-button variant="secondary" class="!bg-surface !text-content">Reply to Sarah</x-button>
    </div>
</x-card>
@endsection
