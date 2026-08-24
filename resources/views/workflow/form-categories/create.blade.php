@extends('layouts.admin', ['title' => 'Nouvelle catégorie'])

@section('content')
    <x-page-header title="Nouvelle catégorie de formulaires">
        <x-slot:actions>
            <x-button href="{{ route('workflow.admin.form-categories.index') }}" variant="secondary" icon="arrow-left">Retour</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('workflow.admin.form-categories.store') }}" class="max-w-xl space-y-4">
        @csrf
        <x-card>
            <div class="space-y-4">
                <x-form-input name="name" label="Nom" required hint="Ex. Gestion Client, Ressources Humaines, Finance." />
                <x-form-input name="code" label="Code" required hint="Unique sur l'ensemble de la plateforme." />
                <x-form-textarea name="description" label="Description" />
                <x-form-checkbox name="is_active" label="Catégorie active" :checked="true" />
            </div>
        </x-card>

        <div class="flex items-center gap-2.5">
            <x-button type="submit" icon="plus">Créer la catégorie</x-button>
            <x-button href="{{ route('workflow.admin.form-categories.index') }}" variant="secondary">Annuler</x-button>
        </div>
    </form>
@endsection
