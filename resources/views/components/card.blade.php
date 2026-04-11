@props([
    'title' => null,
    'subtitle' => null,
    'padding' => 'p-6',
])

<section {{ $attributes->merge(['class' => "rounded-xl border border-gray-200 bg-white shadow-sm {$padding}"]) }}>
    @if ($title)
        <header class="mb-4">
            <h3 class="text-lg font-semibold tracking-tight text-gray-900">{{ $title }}</h3>
            @if ($subtitle)
                <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
            @endif
        </header>
    @endif

    {{ $slot }}
</section>
