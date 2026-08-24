@props(['variant' => 'primary', 'size' => 'md', 'href' => null, 'type' => 'button', 'icon' => null])

@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-xl font-semibold transition focus:outline-none focus:ring-4 disabled:cursor-not-allowed disabled:opacity-50 active:scale-[0.98]';

    $sizes = [
        'sm' => 'px-3 py-1.5 text-[13px]',
        'md' => 'px-4 py-2.5 text-sm',
    ];

    $variants = [
        'primary' => 'bg-brand-blue text-white shadow-sm shadow-brand-blue/30 hover:bg-brand-blue-dark hover:shadow-md hover:shadow-brand-blue/30 focus:ring-brand-blue/25',
        'secondary' => 'border border-brand-border bg-white text-brand-navy shadow-sm hover:border-brand-blue/30 hover:bg-brand-blue/[0.04] focus:ring-slate-200',
        'danger' => 'border border-red-200 bg-white text-brand-danger shadow-sm hover:bg-red-50 focus:ring-red-100',
        'ghost' => 'text-slate-500 hover:bg-slate-100 hover:text-brand-navy focus:ring-slate-200',
    ];

    $classes = $base . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['primary']);
    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @else type="{{ $type }}" @endif
    {{ $attributes->class($classes) }}
>
    @if ($icon)
        @include('layouts.partials.icon', ['name' => $icon, 'class' => 'h-4 w-4 shrink-0'])
    @endif
    {{ $slot }}
</{{ $tag }}>
