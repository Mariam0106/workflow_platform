@extends('layouts.admin', ['title' => 'Nouveau département'])

@section('content')
    <x-page-header title="Nouveau département">
        <x-slot:actions>
            <x-button href="{{ route('organisation.departments.index') }}" variant="secondary" icon="arrow-left">Retour</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('organisation.departments.store') }}" class="max-w-xl space-y-4">
        @csrf
        <x-card>
            <div class="space-y-4">
                <x-form-select name="entity_id" label="Entité" :options="$entities" required />
                <x-form-input name="name" label="Nom" required />
                <x-form-input name="code" label="Code" required hint="Unique au sein de l'entité sélectionnée." />
                <x-form-select name="manager_id" label="Responsable" :options="$users" labelKey="full_name" hint="Utilisateur vers qui router les étapes de workflow configurées sur « Responsable de département »." />
                <x-form-textarea name="description" label="Description" />
                <x-form-checkbox name="is_active" label="Département actif" :checked="true" />
            </div>
        </x-card>

        <div class="flex items-center gap-2.5">
            <x-button type="submit" icon="plus">Créer le département</x-button>
            <x-button href="{{ route('organisation.departments.index') }}" variant="secondary">Annuler</x-button>
        </div>
    </form>
@endsection
