@props([
    'title' => null,
    'subtitle' => null,
    'padding' => 'p-6',
])

<section {{ $attributes->merge(['class' => "bg-surface rounded-2xl shadow-sm border border-line {$padding}"]) }}>
    @if ($title)
        <header class="mb-4">
            <h3 class="text-lg font-bold tracking-tight text-content">{{ $title }}</h3>
            @if ($subtitle)
                <p class="text-sm text-content-muted mt-1">{{ $subtitle }}</p>
            @endif
        </header>
    @endif

    {{ $slot }}
</section>
