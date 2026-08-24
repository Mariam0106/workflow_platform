@extends('layouts.admin', ['title' => 'Nouvelle entité'])

@section('content')
    <x-page-header title="Nouvelle entité">
        <x-slot:actions>
            <x-button href="{{ route('organisation.entities.index') }}" variant="secondary" icon="arrow-left">Retour</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('organisation.entities.store') }}" class="max-w-xl space-y-4">
        @csrf
        <x-card>
            <div class="space-y-4">
                <x-form-input name="name" label="Nom" required />
                <x-form-input name="code" label="Code" required hint="Unique sur l'ensemble de la plateforme." />
                <x-form-select name="manager_id" label="Responsable" :options="$users" labelKey="full_name" hint="Utilisateur vers qui router les étapes de workflow configurées sur « Responsable d'entité »." />
                <x-form-textarea name="description" label="Description" />
                <x-form-checkbox name="is_active" label="Entité active" :checked="true" />
            </div>
        </x-card>

        <div class="flex items-center gap-2.5">
            <x-button type="submit" icon="plus">Créer l'entité</x-button>
            <x-button href="{{ route('organisation.entities.index') }}" variant="secondary">Annuler</x-button>
        </div>
    </form>
@endsection
