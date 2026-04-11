@props([
    'type' => 'text',
])

@php
    $name = $attributes->get('name');
    $hasError = $name ? $errors->has($name) : false;
@endphp

<input
    type="{{ $type }}"
    aria-invalid="{{ $hasError ? 'true' : 'false' }}"
    {{ $attributes->merge(['class' => 'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 shadow-sm outline-none transition focus:ring-2 '.($hasError ? 'border-red-300 focus:border-red-500 focus:ring-red-100' : 'border-gray-200 focus:border-indigo-500 focus:ring-indigo-100')]) }}
/>
