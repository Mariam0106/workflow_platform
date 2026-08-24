@props(['label', 'value', 'icon' => 'file', 'accent' => 'blue', 'href' => null])

@php
    $accents = [
        'blue' => 'bg-brand-blue/10 text-brand-blue',
        'success' => 'bg-green-50 text-brand-success',
        'warning' => 'bg-amber-50 text-brand-warning',
        'danger' => 'bg-red-50 text-brand-danger',
        'slate' => 'bg-slate-100 text-slate-500',
    ];
    $accentClass = $accents[$accent] ?? $accents['blue'];
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if($href) href="{{ $href }}" @endif
    class="flex items-center gap-3.5 rounded-xl border border-brand-border bg-white p-4 transition {{ $href ? 'hover:border-brand-blue/40 hover:shadow-sm' : '' }}">
    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ $accentClass }}">
        @include('layouts.partials.icon', ['name' => $icon, 'class' => 'h-4 w-4'])
    </span>
    <div class="min-w-0">
        <p class="text-2xl font-semibold leading-tight text-brand-navy">{{ $value }}</p>
        <p class="truncate text-[13px] text-slate-500">{{ $label }}</p>
    </div>
</{{ $tag }}>
