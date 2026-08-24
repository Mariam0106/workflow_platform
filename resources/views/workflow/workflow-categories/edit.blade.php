@extends('layouts.admin', ['title' => 'Modifier ' . $workflowCategory->name])

@section('content')
    <x-page-header :title="'Modifier ' . $workflowCategory->name">
        <x-slot:actions>
            <x-button href="{{ route('workflow.admin.workflow-categories.index') }}" variant="secondary" icon="arrow-left">Retour</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('workflow.admin.workflow-categories.update', $workflowCategory) }}" class="max-w-xl space-y-4">
        @csrf
        @method('PUT')
        <x-card>
            <div class="space-y-4">
                <x-form-input name="name" label="Nom" required :value="$workflowCategory->name" />
                <x-form-input name="code" label="Code" required :value="$workflowCategory->code" hint="Unique sur l'ensemble de la plateforme." />
                <x-form-textarea name="description" label="Description" :value="$workflowCategory->description" />
                <x-form-checkbox name="is_active" label="Catégorie active" :checked="$workflowCategory->is_active" />
            </div>
        </x-card>

        <div class="flex items-center gap-2.5">
            <x-button type="submit" icon="check">Enregistrer</x-button>
            <x-button href="{{ route('workflow.admin.workflow-categories.index') }}" variant="secondary">Annuler</x-button>
        </div>
    </form>
@endsection
