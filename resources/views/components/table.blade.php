@props([
    'headers' => [],
])

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm']) }}>
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        @if (!empty($headers))
            <thead class="bg-gray-50">
                <tr>
                    @foreach ($headers as $header)
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
        @endif

        <tbody class="divide-y divide-gray-100">
            {{ $slot }}
        </tbody>
    </table>
</div>
