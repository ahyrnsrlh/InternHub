@extends('layouts.app')

@section('title', 'Monthly Logbook Summary')
@section('active_menu', 'summary')
@section('nav_title', 'Executive Portal')
@section('search_placeholder', 'Search entries...')

@section('content')
@php
    $weeks = [
        ['title' => 'Week 01: Operations Strategy', 'period' => 'Nov 01 - Nov 07', 'summary' => 'Shadowed COO meetings and mapped key bottlenecks in supply chain.'],
        ['title' => 'Week 02: Financial Oversight', 'period' => 'Nov 08 - Nov 14', 'summary' => 'Audited Q3 reports and prepared board-level visualization deck.'],
        ['title' => 'Week 03: Market Penetration', 'period' => 'Nov 15 - Nov 21', 'summary' => 'Delivered competitor matrix and strategic expansion opportunities.'],
    ];
@endphp

<section class="space-y-2">
    <p class="inline-flex rounded-full bg-success-soft px-3 py-1 text-[11px] uppercase tracking-[0.2em] font-bold text-success">Documentation Phase</p>
    <h1 class="text-5xl font-black tracking-tight text-content">Monthly Performance Review</h1>
    <p class="text-content-muted max-w-2xl">A comprehensive overview of executive contributions, milestones achieved, and strategic reflections.</p>
</section>

<section class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <x-card><p class="text-xs uppercase tracking-widest text-content-muted font-bold">Approval Status</p><p class="text-xl font-bold mt-2">Pending Mentor Review</p></x-card>
    <x-card><p class="text-xs uppercase tracking-widest text-content-muted font-bold">Days Logged</p><p class="text-xl font-bold mt-2">22 / 22 Business Days</p></x-card>
    <div class="flex items-center md:justify-end"><x-button>Export as PDF</x-button></div>
</section>

<section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-4">
        @foreach ($weeks as $week)
            <x-card>
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div>
                        <p class="text-lg font-bold text-content">{{ $week['title'] }}</p>
                        <p class="text-xs text-content-muted">{{ $week['period'] }}</p>
                    </div>
                    <span class="internhub-chip bg-success-soft text-success">Completed</span>
                </div>
                <p class="text-sm text-content-muted mt-4">{{ $week['summary'] }}</p>
            </x-card>
        @endforeach
    </div>

    <x-card title="Executive Summary" subtitle="Auto-saved 2 minutes ago">
        <textarea rows="16" class="w-full rounded-lg border-line bg-surface-subtle text-sm">This month represented a pivot from observational learning to active tactical contribution across Operations and Finance.

The highlight was presenting the Supply Chain Bottleneck Analysis to the executive team.

Next month focus: deeper regional data granularity and remote collaboration standards.</textarea>
        <div class="mt-4 rounded-lg border border-line bg-surface-subtle p-4 text-sm text-content-muted">
            "Your analysis on the supply chain was remarkably precise. Let's expand those findings in our next 1-on-1." - Sarah Chen, COO
        </div>
    </x-card>
</section>
@endsection
