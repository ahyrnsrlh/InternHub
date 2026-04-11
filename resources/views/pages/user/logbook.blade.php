@extends('layouts.user')

@section('title', 'Logbook')
@section('header', 'Daily Logbook')

@section('content')
@if (session('status'))
    <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
        {{ session('status') }}
    </div>
@endif

<div class="grid gap-6 lg:grid-cols-3">
    <x-card class="lg:col-span-1" title="Add Daily Activity" subtitle="Write what you completed today.">
        <form method="POST" action="{{ route('user.logbook.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Date</label>
                <x-input type="date" name="log_date" :value="old('log_date')" />
                @error('log_date')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Department</label>
                <x-input name="department" :value="old('department', auth()->user()->department)" placeholder="Department" />
                @error('department')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Activity</label>
                <textarea name="summary" class="min-h-28 w-full rounded-xl border border-gray-200 px-3.5 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 shadow-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" placeholder="Explain today's work progress...">{{ old('summary') }}</textarea>
                @error('summary')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Hours</label>
                <x-input type="number" step="0.5" min="0" max="24" name="hours" :value="old('hours', 8)" />
                @error('hours')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <x-button type="submit" class="w-full">Save Log</x-button>
        </form>
    </x-card>

    <x-card class="lg:col-span-2" title="Activity Timeline" subtitle="Your recent internship activities.">
        @if ($logs->count())
            <ol class="relative ml-3 space-y-6 border-l border-gray-200 pl-6">
                @foreach ($logs as $log)
                    <li class="relative">
                        <span class="absolute -left-[31px] top-1.5 h-3 w-3 rounded-full bg-indigo-600"></span>
                        <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">{{ optional($log->log_date)->format('d M Y') }}</p>
                        <p class="mt-1 text-sm text-gray-700">{{ $log->summary }}</p>
                        <p class="mt-1 text-xs text-gray-500">Department: {{ $log->department }} · Hours: {{ $log->hours }}</p>
                    </li>
                @endforeach
            </ol>
            <div class="mt-4">{{ $logs->links() }}</div>
        @else
            <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center text-sm text-gray-500">
                No logbook data yet.
            </div>
        @endif
    </x-card>
</div>
@endsection
