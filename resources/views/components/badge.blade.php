@props([
    'variant' => 'default',
])

@php
    $variants = [
        'default' => 'bg-gray-100 text-gray-700',
        'success' => 'bg-green-100 text-green-700',
        'danger' => 'bg-red-100 text-red-600',
        'warning' => 'bg-amber-100 text-amber-700',
        'info' => 'bg-indigo-100 text-indigo-700',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold '.($variants[$variant] ?? $variants['default'])]) }}>
    {{ $slot }}
</span>
