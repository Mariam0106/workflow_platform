@extends('layouts.admin', ['title' => 'Nouveau formulaire'])

@section('content')
    <x-page-header title="Nouveau formulaire" description="Vous pourrez ajouter des champs juste après.">
        <x-slot:actions>
            <x-button href="{{ route('workflow.admin.forms.index') }}" variant="secondary" icon="arrow-left">Retour</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('workflow.admin.forms.store') }}" class="max-w-xl space-y-4">
        @csrf
        <x-card>
            <div class="space-y-4">
                <x-form-input name="name" label="Nom" required />
                <x-form-input name="code" label="Code" required hint="Identifiant technique unique (lettres, chiffres, tirets)." />
                <x-form-select name="form_category_id" label="Catégorie" :options="$formCategories" required />
                <x-form-select name="workflow_id" label="Workflow" :options="$workflows" required
                                :value="old('workflow_id', $preselectedWorkflowId)"
                                hint="Doit être publié avant que ce formulaire puisse lui-même être publié." />
                <x-form-textarea name="description" label="Description" />
            </div>
        </x-card>

        <div class="flex items-center gap-2.5">
            <x-button type="submit" icon="plus">Créer et ajouter des champs</x-button>
            <x-button href="{{ route('workflow.admin.forms.index') }}" variant="secondary">Annuler</x-button>
        </div>
    </form>
@endsection
