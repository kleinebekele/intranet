@props(['active' => false])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium bg-gray-900 text-white'
            : 'flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
