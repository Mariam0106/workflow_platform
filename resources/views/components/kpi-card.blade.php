@props(['label', 'value', 'icon' => 'dashboard', 'accent' => 'neutral'])

@php
    $accents = [
        'neutral' => 'bg-slate-100 text-slate-500',
        'blue' => 'bg-brand-blue/10 text-brand-blue',
        'warning' => 'bg-amber-50 text-brand-warning',
        'success' => 'bg-green-50 text-brand-success',
        'danger' => 'bg-red-50 text-brand-danger',
    ];
    $accentClass = $accents[$accent] ?? $accents['neutral'];
@endphp

<div class="rounded-xl border border-brand-border bg-white p-5">
    <div class="flex items-center justify-between">
        <span class="text-[13px] font-medium text-slate-500">{{ $label }}</span>
        <span class="flex h-8 w-8 items-center justify-center rounded-lg {{ $accentClass }}">
            @include('layouts.partials.icon', ['name' => $icon, 'class' => 'h-4 w-4'])
        </span>
    </div>
    <p class="mt-3 text-[26px] font-semibold tracking-tight text-brand-navy tabular-nums">{{ $value }}</p>
</div>
