@extends('layouts.admin', ['title' => 'Modifier ' . $department->name])

@section('content')
    <x-page-header :title="'Modifier ' . $department->name">
        <x-slot:actions>
            <x-button href="{{ route('organisation.departments.index') }}" variant="secondary" icon="arrow-left">Retour</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('organisation.departments.update', $department) }}" class="max-w-xl space-y-4">
        @csrf
        @method('PUT')
        <x-card>
            <div class="space-y-4">
                <x-form-select name="entity_id" label="Entité" :options="$entities" required :value="$department->entity_id" />
                <x-form-input name="name" label="Nom" required :value="$department->name" />
                <x-form-input name="code" label="Code" required :value="$department->code" hint="Unique au sein de l'entité sélectionnée." />
                <x-form-select name="manager_id" label="Responsable" :options="$users" labelKey="full_name" :value="$department->manager_id" hint="Utilisateur vers qui router les étapes de workflow configurées sur « Responsable de département »." />
                <x-form-textarea name="description" label="Description" :value="$department->description" />
                <x-form-checkbox name="is_active" label="Département actif" :checked="$department->is_active" />
            </div>
        </x-card>

        <div class="flex items-center gap-2.5">
            <x-button type="submit" icon="check">Enregistrer</x-button>
            <x-button href="{{ route('organisation.departments.index') }}" variant="secondary">Annuler</x-button>
        </div>
    </form>
@endsection
