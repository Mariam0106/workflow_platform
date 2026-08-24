@extends('layouts.admin', ['title' => 'Modifier ' . $step->name])

@section('content')
    <x-page-header :title="'Modifier « ' . $step->name . ' »'" :description="'Workflow « ' . $workflow->name . ' »'">
        <x-slot:actions>
            <x-button href="{{ route('workflow.admin.workflows.edit', $workflow) }}" variant="secondary" icon="arrow-left">Retour</x-button>
        </x-slot:actions>
    </x-page-header>

    @php
        $currentType = old('validator_type', $step->validator_type->value);
        $currentBusinessFunctionRef = old('validator_reference_business_function', $step->validator_type === \App\Enums\ValidatorType::BusinessFunction ? $step->validator_reference : null);
        $currentUserRef = old('validator_reference_user', $step->validator_type === \App\Enums\ValidatorType::User ? $step->validator_reference : null);
    @endphp

    <form method="POST" action="{{ route('workflow.admin.workflows.steps.update', [$workflow, $step]) }}" class="max-w-xl space-y-4">
        @csrf
        @method('PUT')
        <x-card>
            <div class="space-y-4">
                <x-form-input name="name" label="Nom" required :value="$step->name" />
                <x-form-input name="code" label="Code" required :value="$step->code" />
                <x-form-textarea name="description" label="Description" :value="$step->description" />

                <div>
                    <label for="validator_type" class="mb-1.5 block text-[13px] font-medium text-slate-700">Type de validateur <span class="text-brand-danger">*</span></label>
                    <select id="validator_type" name="validator_type" required onchange="updateValidatorFields()"
                            class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                        @if ($currentType === 'ROLE')
                            <option value="ROLE" selected>Rôle Applicatif (Validator) — ancien réglage</option>
                        @endif
                        <option value="BUSINESS_FUNCTION" @selected($currentType === 'BUSINESS_FUNCTION')>Fonction Métier</option>
                        <option value="USER" @selected($currentType === 'USER')>Utilisateur désigné</option>
                        <option value="N_PLUS_1" @selected($currentType === 'N_PLUS_1')>Responsable direct (N+1) du demandeur</option>
                        <option value="ENTITY_MANAGER" @selected($currentType === 'ENTITY_MANAGER')>Responsable de l'Entité du demandeur</option>
                        <option value="DEPARTMENT_MANAGER" @selected($currentType === 'DEPARTMENT_MANAGER')>Responsable du Département du demandeur</option>
                    </select>
                    @error('validator_type') <p class="mt-1 text-xs text-brand-danger">{{ $message }}</p> @enderror
                </div>

                <div id="field-business-function">
                    <x-form-select name="validator_reference_business_function" label="Fonction Métier validatrice" :options="$businessFunctions" placeholder="—"
                                    :value="$currentBusinessFunctionRef" hint="Toute personne occupant cette fonction pourra valider cette étape." />
                </div>

                <div id="field-user-picker">
                    <x-user-picker name="validator_reference_user" label="Utilisateur validateur" :users="$users" :entities="$entities" :departments="$departments"
                                    sync-business-function-field="validator_reference_business_function"
                                    :value="$currentUserRef" />
                    <p id="user-picker-preview-hint" class="mt-1.5 hidden text-xs text-slate-400">Aperçu des personnes concernées - aucune sélection à faire, n'importe laquelle d'entre elles pourra valider.</p>
                </div>

                <div id="field-auto-info" class="hidden rounded-lg border border-brand-border bg-slate-50/60 px-3.5 py-3 text-[13px] text-slate-600"></div>

                <div id="field-manager-preview" class="hidden">
                    <p class="mb-1.5 block text-[13px] font-medium text-slate-700">Responsables actuellement configurés</p>
                    <ul id="manager-preview-list" class="divide-y divide-brand-border rounded-lg border border-brand-border bg-white text-[13px]"></ul>
                </div>

                <x-form-checkbox name="is_end" label="Étape de fin" :checked="$step->is_end" hint="Un workflow doit avoir au moins une étape de fin." />
            </div>
        </x-card>

        <div class="flex items-center gap-2.5">
            <x-button type="submit" icon="check">Enregistrer</x-button>
            <x-button href="{{ route('workflow.admin.workflows.edit', $workflow) }}" variant="secondary">Annuler</x-button>
        </div>
    </form>

    <script>
        var validatorAutoMessages = {
            ROLE: 'Le validateur sera automatiquement toute personne ayant le rôle applicatif Validator - ancien réglage, rien à choisir ici.',
            N_PLUS_1: "Le validateur sera automatiquement le responsable direct (N+1) de la personne qui a soumis la demande - variable selon le demandeur, il n'y a donc pas un nom unique à afficher ici.",
        };

        var entityManagers = @json($entities->map(fn ($e) => ['name' => $e->name, 'manager' => $e->manager?->full_name]));
        var departmentManagers = @json($departments->map(fn ($d) => ['name' => $d->name, 'manager' => $d->manager?->full_name]));

        function renderManagerPreview(rows) {
            var list = document.getElementById('manager-preview-list');
            list.innerHTML = rows.map(function (row) {
                var manager = row.manager
                    ? '<span class="text-brand-navy">' + row.manager + '</span>'
                    : '<span class="text-brand-danger">Aucun responsable configuré</span>';
                return '<li class="flex items-center justify-between gap-3 px-3.5 py-2.5"><span class="text-slate-600">' + row.name + '</span>' + manager + '</li>';
            }).join('');
        }

        function updateValidatorFields() {
            var type = document.getElementById('validator_type').value;
            var businessFunctionField = document.getElementById('field-business-function');
            var userPickerField = document.getElementById('field-user-picker');
            var userPickerHint = document.getElementById('user-picker-preview-hint');
            var autoInfoField = document.getElementById('field-auto-info');
            var managerPreviewField = document.getElementById('field-manager-preview');

            businessFunctionField.classList.toggle('hidden', type !== 'BUSINESS_FUNCTION');

            var showUserPicker = type === 'USER' || type === 'BUSINESS_FUNCTION';
            userPickerField.classList.toggle('hidden', !showUserPicker);
            userPickerHint.classList.toggle('hidden', type !== 'BUSINESS_FUNCTION');

            if (type === 'ENTITY_MANAGER') {
                renderManagerPreview(entityManagers);
                managerPreviewField.classList.remove('hidden');
            } else if (type === 'DEPARTMENT_MANAGER') {
                renderManagerPreview(departmentManagers);
                managerPreviewField.classList.remove('hidden');
            } else {
                managerPreviewField.classList.add('hidden');
            }

            if (validatorAutoMessages[type]) {
                autoInfoField.textContent = validatorAutoMessages[type];
                autoInfoField.classList.remove('hidden');
            } else {
                autoInfoField.classList.add('hidden');
            }
        }

        updateValidatorFields();
    </script>
@endsection
