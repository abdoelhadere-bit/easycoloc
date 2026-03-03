@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl bg-brand-50 text-brand-700 transition-all duration-200 group border border-brand-100 shadow-sm'
            : 'flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl text-zinc-600 hover:bg-zinc-50 hover:text-zinc-900 transition-all duration-200 group border border-transparent';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
