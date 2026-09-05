@props(['active', 'icon'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-4 py-3 text-sm font-semibold text-indigo-600 bg-indigo-50/80 rounded-xl transition-all duration-150'
            : 'flex items-center px-4 py-3 text-sm font-medium text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-xl transition-all duration-150';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $icon ?? '' }}
    {{ $slot }}
</a>