@props([
    'variant' => 'primary',
    'type' => 'button',
])

@php
    $variants = [
        'primary' => 'bg-indigo-600 text-white hover:bg-indigo-700 focus-visible:ring-indigo-200',
        'secondary' => 'bg-gray-100 text-gray-700 hover:bg-gray-200 focus-visible:ring-gray-200',
        'success' => 'bg-green-500 text-white hover:bg-green-600 focus-visible:ring-green-200',
        'danger' => 'bg-red-500 text-white hover:bg-red-600 focus-visible:ring-red-200',
        'ghost' => 'bg-transparent text-gray-600 hover:bg-gray-100 focus-visible:ring-gray-200',
    ];

    $classes = $variants[$variant] ?? $variants['primary'];
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold shadow-sm transition focus-visible:outline-none focus-visible:ring-4 {$classes}"]) }}
>
    {{ $slot }}
</button>
