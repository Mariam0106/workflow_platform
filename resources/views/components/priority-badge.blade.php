@props(['priority'])

@php
    $tone = match ($priority?->value ?? null) {
        'Urgent' => 'danger',
        'High' => 'warning',
        default => null,
    };
@endphp

@if ($tone)
    <x-badge :tone="$tone">{{ $priority->label() }}</x-badge>
@endif
