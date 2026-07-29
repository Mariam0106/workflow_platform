{{--
    ==========================================================================
    <x-kpi-card>
    ==========================================================================
    AJOUT (round 3 - correction) : cf. page-header.blade.php - composant
    manquant dans le projet fourni, sans rapport avec la fonctionnalité de
    rôles multiples (N-N) des rounds précédents.

    Props : label (string), value (int), icon (string, cf. layouts.partials.icon),
            accent (blue|warning|success|danger)
    ==========================================================================
--}}
@php
    $accentClasses = match ($accent ?? 'blue') {
        'warning' => 'bg-amber-50 text-amber-600',
        'success' => 'bg-emerald-50 text-emerald-600',
        'danger' => 'bg-red-50 text-red-600',
        default => 'bg-brand-blue/10 text-brand-blue',
    };
@endphp

<div class="rounded-xl border border-brand-border bg-white p-4">
    <span class="flex h-8 w-8 items-center justify-center rounded-lg {{ $accentClasses }}">
        @include('layouts.partials.icon', ['name' => $icon ?? '', 'class' => 'h-4 w-4'])
    </span>
    <p class="mt-3 text-2xl font-semibold tracking-tight text-brand-navy">{{ $value }}</p>
    <p class="mt-0.5 text-xs text-slate-500">{{ $label }}</p>
</div>
