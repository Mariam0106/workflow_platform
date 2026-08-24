@props(['status'])

@php
    [$tone, $label] = match ($status?->value ?? null) {
        'Completed' => ['success', 'Validée'],
        'Rejected' => ['danger', 'Refusée'],
        'Draft' => ['slate', 'Brouillon'],
        'Submitted' => ['warning', 'En cours'],
        default => ['slate', '—'],
    };
@endphp

<x-badge :tone="$tone">{{ $label }}</x-badge>
