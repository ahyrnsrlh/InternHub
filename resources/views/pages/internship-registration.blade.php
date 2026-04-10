@extends('layouts.app')

@section('title', 'Internship Registration')
@section('active_menu', 'registration')
@section('nav_title', 'Executive Portal')
@section('search_placeholder', 'Search resources...')

@section('content')
<section class="space-y-2">
    <h1 class="text-5xl font-black tracking-tight text-content">Internship Onboarding.</h1>
    <p class="text-content-muted max-w-2xl">Please provide the credentials required to architect your career path within our ecosystem.</p>
</section>

<section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <x-card class="lg:col-span-2" title="Academic Credentials" subtitle="Step 02 of 04">
        <form class="space-y-6" method="POST" action="#" enctype="multipart/form-data">
            <div class="grid sm:grid-cols-2 gap-4">
                <label class="space-y-2 text-xs font-bold uppercase tracking-wider text-content-muted">
                    University Name
                    <input type="text" class="w-full rounded-lg border-line bg-surface-subtle text-sm" placeholder="Stanford University">
                </label>
                <label class="space-y-2 text-xs font-bold uppercase tracking-wider text-content-muted">
                    Major / Discipline
                    <input type="text" class="w-full rounded-lg border-line bg-surface-subtle text-sm" placeholder="Strategic Management">
                </label>
            </div>

            <label class="space-y-2 block text-xs font-bold uppercase tracking-wider text-content-muted">
                Expected Graduation Date
                <input type="date" class="w-full rounded-lg border-line bg-surface-subtle text-sm">
            </label>

            <div>
                <p class="text-base font-bold text-content mb-3">Architect Your Mentorship</p>
                <div class="space-y-3">
                    @foreach ([['name' => 'Dr. Elena Thorne', 'role' => 'VP of Operations'], ['name' => 'Jonathan Sterling', 'role' => 'Chief Product Officer']] as $mentor)
                        <label class="flex items-center justify-between rounded-xl border border-line p-4 cursor-pointer hover:bg-surface-subtle">
                            <span>
                                <span class="block font-bold text-content">{{ $mentor['name'] }}</span>
                                <span class="text-sm text-content-muted">{{ $mentor['role'] }}</span>
                            </span>
                            <input type="radio" name="mentor" class="text-content">
                        </label>
                    @endforeach
                </div>
            </div>

            <label class="block rounded-xl border-2 border-dashed border-line-strong p-8 text-center cursor-pointer hover:bg-surface-subtle">
                <span class="material-symbols-outlined text-3xl text-content-muted">cloud_upload</span>
                <span class="block mt-2 font-semibold text-content">Upload Portfolio & CV</span>
                <span class="block text-xs text-content-muted mt-1">PDF or DOCX (max 15MB)</span>
                <input type="file" class="hidden">
            </label>

            <div class="flex justify-between items-center">
                <x-button variant="ghost">Back</x-button>
                <x-button type="submit">Next Module</x-button>
            </div>
        </form>
    </x-card>

    <div class="space-y-4">
        <x-card title="Elite Access">
            <p class="text-sm text-content-muted">Your registration connects you with 2,400+ industry leaders and executive mentors.</p>
        </x-card>
        <x-card class="bg-success text-success-foreground border-success" title="Mentor Insight">
            <p class="text-sm">Select a mentor based on project synergy and long-term career alignment.</p>
        </x-card>
    </div>
</section>
@endsection
