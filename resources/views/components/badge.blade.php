@props(['color' => 'neutral'])

@php
    $palette = [
        'success' => 'bg-green-50 text-brand-success ring-1 ring-inset ring-green-600/15',
        'warning' => 'bg-amber-50 text-brand-warning ring-1 ring-inset ring-amber-600/15',
        'danger' => 'bg-red-50 text-brand-danger ring-1 ring-inset ring-red-600/15',
        'neutral' => 'bg-slate-100 text-slate-500 ring-1 ring-inset ring-slate-500/10',
    ];
    $styles = $palette[$color] ?? $palette['neutral'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {$styles}"]) }}>
    {{ $slot }}
</span>
