@props(['tone' => 'slate'])

@php
    $tones = [
        'slate' => 'bg-slate-100 text-slate-600',
        'blue' => 'bg-brand-blue/10 text-brand-blue',
        'success' => 'bg-green-50 text-brand-success',
        'warning' => 'bg-amber-50 text-brand-warning',
        'danger' => 'bg-red-50 text-brand-danger',
        'navy' => 'bg-brand-navy/5 text-brand-navy',
    ];
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-medium', $tones[$tone] ?? $tones['slate']]) }}>
    {{ $slot }}
</span>
