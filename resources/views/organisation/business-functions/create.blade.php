@extends('layouts.admin', ['title' => 'Nouvelle fonction métier'])

@section('content')
    <x-page-header title="Nouvelle fonction métier">
        <x-slot:actions>
            <x-button href="{{ route('organisation.business-functions.index') }}" variant="secondary" icon="arrow-left">Retour</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('organisation.business-functions.store') }}" class="max-w-xl space-y-4">
        @csrf
        <x-card>
            <div class="space-y-4">
                <x-form-input name="name" label="Nom" required hint="Ex. Commercial, Crédit Client, DAF, DG." />
                <x-form-input name="code" label="Code" required hint="Unique sur l'ensemble de la plateforme." />
                <x-form-textarea name="description" label="Description" />
                <x-form-checkbox name="is_active" label="Fonction active" :checked="true" />
            </div>
        </x-card>

        <div class="flex items-center gap-2.5">
            <x-button type="submit" icon="plus">Créer la fonction</x-button>
            <x-button href="{{ route('organisation.business-functions.index') }}" variant="secondary">Annuler</x-button>
        </div>
    </form>
@endsection
