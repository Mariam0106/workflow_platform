@props(['status'])

@php
    [$tone, $label] = match ($status?->value ?? null) {
        'Draft' => ['slate', 'Brouillon'],
        'Published' => ['success', 'Publié'],
        'Archived' => ['warning', 'Archivé'],
        default => ['slate', '—'],
    };
@endphp

<x-badge :tone="$tone">{{ $label }}</x-badge>
