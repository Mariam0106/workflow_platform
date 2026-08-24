@props(['role'])

@php
    // Une seule source de vérité pour la couleur d'un Rôle Applicatif -
    // toute la plateforme (sidebar, sélecteur de rôle, listes Admin)
    // utilise ce composant plutôt que de redéfinir la palette ailleurs.
    $tone = match ($role?->code?->value ?? null) {
        \App\Enums\ApplicationRoleCode::Administrator->value => 'navy',
        \App\Enums\ApplicationRoleCode::Validator->value => 'blue',
        \App\Enums\ApplicationRoleCode::User->value => 'slate',
        default => 'slate',
    };
@endphp

@if ($role)
    <x-badge :tone="$tone">{{ $role->name }}</x-badge>
@endif
