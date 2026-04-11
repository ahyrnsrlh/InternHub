@extends('layouts.user')

@section('title', 'Attendance Reports')
@section('header', 'Attendance Reports')

@section('content')
<div class="space-y-6">
    @if (session('status'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    <x-card>
        <form method="GET" action="{{ route('user.reports.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700">Filter by Date</label>
                <x-input type="date" name="date" :value="$filterDate" />
            </div>
            <x-button variant="secondary" type="submit">Apply Filter</x-button>
            @if($filterDate)
                <a href="{{ route('user.reports.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">Reset</a>
            @endif
        </form>
    </x-card>

    <x-card title="Attendance Table" subtitle="Daily attendance records with verification status.">
        @if ($reports->count())
            <div class="overflow-hidden rounded-xl border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Date</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Summary</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($reports as $report)
                            <tr>
                                <td class="px-4 py-3 text-gray-700">{{ optional($report->log_date)->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $report->summary }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $report->status === 'approved' ? 'bg-green-100 text-green-700' : ($report->status === 'revision_required' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-700') }}">
                                        {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $reports->links() }}</div>
        @else
            <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                <p class="text-sm text-gray-500">No report data for the selected date range.</p>
            </div>
        @endif
    </x-card>
</div>
@endsection
