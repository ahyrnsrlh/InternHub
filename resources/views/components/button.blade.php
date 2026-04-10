@props([
    'variant' => 'primary',
    'type' => 'button',
])

@php
    $variants = [
        'primary' => 'bg-primary text-content-inverse hover:bg-primary-hover',
        'secondary' => 'bg-primary-soft text-content hover:bg-line-strong',
        'success' => 'bg-success text-success-foreground hover:bg-success',
        'ghost' => 'bg-transparent text-content-muted hover:bg-primary-soft',
    ];

    $classes = $variants[$variant] ?? $variants['primary'];
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "inline-flex items-center justify-center gap-2 rounded-lg px-5 py-2.5 text-sm font-semibold transition-colors {$classes}"]) }}
>
    {{ $slot }}
</button>
