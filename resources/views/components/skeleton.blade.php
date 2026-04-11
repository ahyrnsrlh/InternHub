@props([
    'class' => '',
])

<div {{ $attributes->merge(['class' => "animate-pulse rounded-xl bg-gray-200/80 {$class}"]) }}></div>
