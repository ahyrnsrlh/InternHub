@extends('layouts.app')

@section('title', 'Daily Logbook')
@section('active_menu', 'logbook')
@section('nav_title', 'Executive Portal')
@section('search_placeholder', 'Search logs or deliverables...')

@section('content')
<section class="space-y-2">
    <h1 class="text-5xl font-black tracking-tight text-content">Submit Daily Log</h1>
    <p class="text-content-muted max-w-2xl">Document your daily milestones, professional insights, and architectural contributions to the internship program.</p>
</section>

<section class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
    <x-card class="lg:col-span-2" title="Log Entry Form">
        <form class="space-y-5" method="POST" action="#">
            <div class="grid sm:grid-cols-2 gap-4">
                <label class="space-y-2 text-xs font-bold uppercase tracking-wider text-content-muted">
                    Log Date
                    <input type="date" class="w-full rounded-lg border-line bg-surface-subtle text-sm">
                </label>
                <label class="space-y-2 text-xs font-bold uppercase tracking-wider text-content-muted">
                    Department
                    <select class="w-full rounded-lg border-line bg-surface-subtle text-sm">
                        <option>Strategic Architecture</option>
                        <option>Market Intelligence</option>
                    </select>
                </label>
            </div>
            <label class="space-y-2 block text-xs font-bold uppercase tracking-wider text-content-muted">
                What did you do today?
                <textarea rows="6" class="w-full rounded-lg border-line bg-surface-subtle text-sm" placeholder="Describe your activities and outcomes..."></textarea>
            </label>
            <label class="space-y-2 block text-xs font-bold uppercase tracking-wider text-content-muted">
                Outputs / Deliverables
                <input type="text" class="w-full rounded-lg border-line bg-surface-subtle text-sm" placeholder="Q3 Market Report Draft, etc">
            </label>
            <x-button type="submit" class="w-full">Submit Log Entry</x-button>
        </form>
    </x-card>

    <div class="space-y-4">
        <x-card class="bg-primary text-content-inverse border-primary-hover" title="Architect's Tip">
            <p class="text-sm text-content-inverse/80">Focus on impact, not just tasks. Explain decisions, constraints, and outcomes.</p>
        </x-card>
        <x-card class="bg-success text-success-foreground border-success" title="Weekly Progress">
            <div class="flex justify-between text-sm"><span>Compliance</span><span class="font-bold">92%</span></div>
            <div class="h-2 rounded-full bg-success-soft mt-3 overflow-hidden"><div class="h-full bg-success-foreground" style="width: 92%"></div></div>
        </x-card>
    </div>
</section>

<x-card title="Submission History">
    <div class="space-y-3">
        @forelse ($history as $item)
            <article class="rounded-xl border border-line p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <p class="font-bold text-content">{{ $item->log_date?->format('M d, Y') }}</p>
                    <p class="text-sm text-content-muted">{{ $item->summary }}</p>
                </div>
                @php($statusLabel = str($item->status)->replace('_', ' ')->headline())
                <span class="internhub-chip {{ $item->status === 'approved' ? 'bg-success-soft text-success' : ($item->status === 'pending' ? 'bg-primary-soft text-content-muted' : 'bg-danger-soft text-danger') }}">{{ $statusLabel }}</span>
            </article>
        @empty
            <p class="text-sm text-content-muted">No log submissions yet.</p>
        @endforelse
    </div>
</x-card>
@endsection
