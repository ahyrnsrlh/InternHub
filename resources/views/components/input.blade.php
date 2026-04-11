@props([
    'type' => 'text',
])

<input
    type="{{ $type }}"
    {{ $attributes->merge(['class' => 'w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100']) }}
/>
