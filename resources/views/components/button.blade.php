@props(['variant' => 'primary'])

@php
    $palette = [
        'primary' => 'bg-brand-blue text-white shadow-sm hover:bg-brand-blue-dark focus-visible:ring-brand-blue/30',
        'secondary' => 'border border-brand-border bg-white text-brand-navy shadow-sm hover:bg-slate-50 focus-visible:ring-brand-blue/20',
        'danger-text' => 'text-brand-danger hover:underline',
        'success-text' => 'text-brand-success hover:underline',
    ];
    $styles = $palette[$variant] ?? $palette['primary'];
@endphp

<button {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-lg px-3.5 py-2 text-sm font-semibold transition focus:outline-none focus-visible:ring-4 {$styles}"]) }}>
    {{ $slot }}
</button>
