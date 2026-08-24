@extends('layouts.admin', ['title' => 'Modifier ' . $entity->name])

@section('content')
    <x-page-header :title="'Modifier ' . $entity->name">
        <x-slot:actions>
            <x-button href="{{ route('organisation.entities.index') }}" variant="secondary" icon="arrow-left">Retour</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('organisation.entities.update', $entity) }}" class="max-w-xl space-y-4">
        @csrf
        @method('PUT')
        <x-card>
            <div class="space-y-4">
                <x-form-input name="name" label="Nom" required :value="$entity->name" />
                <x-form-input name="code" label="Code" required :value="$entity->code" hint="Unique sur l'ensemble de la plateforme." />
                <x-form-select name="manager_id" label="Responsable" :options="$users" labelKey="full_name" :value="$entity->manager_id" hint="Utilisateur vers qui router les étapes de workflow configurées sur « Responsable d'entité »." />
                <x-form-textarea name="description" label="Description" :value="$entity->description" />
                <x-form-checkbox name="is_active" label="Entité active" :checked="$entity->is_active" />
            </div>
        </x-card>

        <div class="flex items-center gap-2.5">
            <x-button type="submit" icon="check">Enregistrer</x-button>
            <x-button href="{{ route('organisation.entities.index') }}" variant="secondary">Annuler</x-button>
        </div>
    </form>
@endsection
