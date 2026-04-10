@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-primary text-start text-base font-medium text-primary bg-primary-soft focus:outline-none focus:text-primary-hover focus:bg-primary-soft focus:border-primary-hover transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-content-muted hover:text-content hover:bg-surface-subtle hover:border-line-strong focus:outline-none focus:text-content focus:bg-surface-subtle focus:border-line-strong transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
