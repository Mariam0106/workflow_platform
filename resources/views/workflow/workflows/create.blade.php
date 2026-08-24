@extends('layouts.admin', ['title' => 'Nouveau workflow'])

@section('content')
    <x-page-header title="Nouveau workflow" description="Vous pourrez ajouter des étapes et des transitions juste après.">
        <x-slot:actions>
            <x-button href="{{ route('workflow.admin.workflows.index') }}" variant="secondary" icon="arrow-left">Retour</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('workflow.admin.workflows.store') }}" class="max-w-xl space-y-4">
        @csrf
        <x-card>
            <div class="space-y-4">
                <x-form-input name="name" label="Nom" required />
                <x-form-input name="code" label="Code" required hint="Identifiant technique unique (lettres, chiffres, tirets)." />
                <x-form-select name="workflow_category_id" label="Catégorie" :options="$workflowCategories" required />
                <x-form-textarea name="description" label="Description" />
            </div>
        </x-card>

        <div class="flex items-center gap-2.5">
            <x-button type="submit" icon="plus">Créer et ajouter des étapes</x-button>
            <x-button href="{{ route('workflow.admin.workflows.index') }}" variant="secondary">Annuler</x-button>
        </div>
    </form>
@endsection
