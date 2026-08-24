@extends('layouts.admin', ['title' => 'Modifier ' . $businessFunction->name])

@section('content')
    <x-page-header :title="'Modifier ' . $businessFunction->name">
        <x-slot:actions>
            <x-button href="{{ route('organisation.business-functions.index') }}" variant="secondary" icon="arrow-left">Retour</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('organisation.business-functions.update', $businessFunction) }}" class="max-w-xl space-y-4">
        @csrf
        @method('PUT')
        <x-card>
            <div class="space-y-4">
                <x-form-input name="name" label="Nom" required :value="$businessFunction->name" />
                <x-form-input name="code" label="Code" required :value="$businessFunction->code" hint="Unique sur l'ensemble de la plateforme." />
                <x-form-textarea name="description" label="Description" :value="$businessFunction->description" />
                <x-form-checkbox name="is_active" label="Fonction active" :checked="$businessFunction->is_active" />
            </div>
        </x-card>

        <div class="flex items-center gap-2.5">
            <x-button type="submit" icon="check">Enregistrer</x-button>
            <x-button href="{{ route('organisation.business-functions.index') }}" variant="secondary">Annuler</x-button>
        </div>
    </form>
@endsection
