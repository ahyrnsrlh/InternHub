@extends('layouts.app')

@section('title', 'Mentor Review Panel')
@section('active_menu', 'reviews')
@section('nav_title', 'Review Panel')
@section('search_placeholder', 'Search review notes...')

@section('content')
<section class="space-y-2">
    <h1 class="text-5xl font-black tracking-tight text-content">Mentor Review Panel</h1>
    <p class="text-content-muted">Assess weekly logbooks and deliver structured feedback for assigned interns.</p>
</section>

<section class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
    <x-card title="Assigned Interns">
        <div class="space-y-3">
            @forelse ($assignedInterns as $intern)
                <article class="rounded-xl border border-line p-4">
                    <p class="font-bold text-content">{{ $intern->name }}</p>
                    <p class="text-xs text-content-muted">{{ $intern->title ?? str($intern->role)->headline() }}</p>
                    <p class="text-xs font-semibold mt-2 {{ $intern->status === 'pending' ? 'text-warning' : 'text-success' }}">{{ str($intern->status)->headline() }}</p>
                </article>
            @empty
                <p class="text-sm text-content-muted">No assigned interns for this mentor yet.</p>
            @endforelse
        </div>
    </x-card>

    <div class="lg:col-span-2 space-y-6">
        <x-card title="Week 04 Logbook - Marcus Chen" subtitle="Submission Period: Oct 23 - Oct 27, 2023">
            <div class="grid md:grid-cols-2 gap-4">
                @forelse ($reviewQueue as $review)
                    <article class="rounded-xl bg-surface-subtle p-4 border border-line">
                        <p class="font-bold text-content">{{ $review->dailyLog?->user?->name ?? 'Intern' }} - {{ $review->dailyLog?->log_date?->format('M d, Y') }}</p>
                        <p class="text-sm text-content-muted mt-2">{{ $review->dailyLog?->summary }}</p>
                        <p class="text-xs font-semibold mt-2 text-content-muted">Status: {{ str($review->status)->replace('_', ' ')->headline() }}</p>
                    </article>
                @empty
                    <p class="text-sm text-content-muted">No review items in queue.</p>
                @endforelse
            </div>
        </x-card>

        <x-card title="Mentor Evaluation">
            <form class="space-y-4" method="POST" action="#">
                <textarea rows="5" class="w-full rounded-lg border-line bg-surface-subtle text-sm" placeholder="Provide guidance on technical skills, professional conduct, and next steps..."></textarea>
                <div class="flex flex-wrap gap-3">
                    <x-button variant="success">Approve Submission</x-button>
                    <x-button variant="secondary">Request Revision</x-button>
                    <x-button variant="ghost">Save Draft</x-button>
                </div>
            </form>
        </x-card>
    </div>
</section>
@endsection
