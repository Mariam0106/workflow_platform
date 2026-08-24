@extends('layouts.admin', ['title' => 'Modifier la transition'])

@section('content')
    <x-page-header title="Modifier la transition" :description="'Workflow « ' . $workflow->name . ' »'">
        <x-slot:actions>
            <x-button href="{{ route('workflow.admin.workflows.edit', $workflow) }}" variant="secondary" icon="arrow-left">Retour</x-button>
        </x-slot:actions>
    </x-page-header>

    @php
        $operatorLabels = [
            '=' => 'Égal à',
            '!=' => 'Différent de',
            '>' => 'Supérieur à',
            '>=' => 'Supérieur ou égal à',
            '<' => 'Inférieur à',
            '<=' => 'Inférieur ou égal à',
            'contains' => 'Contient',
        ];
    @endphp

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <x-card>
            <form method="POST" action="{{ route('workflow.admin.workflows.transitions.update', [$workflow, $transition]) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <x-form-select name="from_step_id" label="Depuis l'étape" :options="$workflow->workflowSteps" required
                                labelKey="name" :value="$transition->from_step_id" />
                <x-form-select name="to_step_id" label="Vers l'étape" :options="$workflow->workflowSteps" required
                                labelKey="name" :value="$transition->to_step_id" />
                <x-form-input name="action_name" label="Nom de l'action" required :value="$transition->action_name" />
                <div>
                    <label for="priority" class="mb-1.5 block text-[13px] font-medium text-slate-700">Priorité <span class="text-brand-danger">*</span></label>
                    <select id="priority" name="priority" required
                            class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                        <option value="50" @selected(old('priority', $transition->priority) == 50)>Normale</option>
                        <option value="80" @selected(old('priority', $transition->priority) == 80)>Élevée</option>
                        <option value="100" @selected(old('priority', $transition->priority) == 100)>Haute</option>
                    </select>
                    @error('priority') <p class="mt-1 text-xs text-brand-danger">{{ $message }}</p> @enderror
                </div>
                <x-form-textarea name="description" label="Description" :value="$transition->description" />
                <x-form-checkbox name="is_default" label="Transition par défaut" :checked="$transition->is_default" />

                <x-button type="submit" size="sm" icon="check">Enregistrer</x-button>
            </form>
        </x-card>

        <x-card :padded="false">
            <div class="border-b border-brand-border px-5 py-4">
                <h2 class="text-[13px] font-semibold uppercase tracking-wide text-slate-500">
                    Conditions ({{ $transition->transitionConditions->count() }})
                </h2>
                <p class="mt-1 text-xs text-slate-500">Toutes évaluées avant l'exécution de cette transition.</p>
            </div>

            @if ($availableFields->isEmpty())
                <x-empty-state icon="file" title="Aucun champ disponible"
                                description="Aucun formulaire n'utilise encore ce workflow. Rattachez d'abord un formulaire pour pouvoir y définir des conditions." />
            @else
                @if ($transition->transitionConditions->isEmpty())
                    <x-empty-state icon="branch" title="Aucune condition" description="Sans condition, cette transition est évaluée sans restriction - utile pour une transition « par défaut »." />
                @else
                    <ul class="divide-y divide-brand-border">
                        @foreach ($transition->transitionConditions as $condition)
                            <li class="flex items-center gap-3 px-5 py-3">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-brand-navy">
                                        {{ $condition->formField?->label }}
                                        {{ $operatorLabels[$condition->operator->value] ?? $condition->operator->value }}
                                        « {{ $condition->expected_value }} »
                                    </p>
                                    @if (! $loop->last)
                                        <p class="text-xs text-slate-400">{{ $condition->logical_operator->value }}</p>
                                    @endif
                                </div>
                                <x-confirm-form
                                    :action="route('workflow.admin.workflows.transitions.conditions.destroy', [$workflow, $transition, $condition])"
                                    method="DELETE"
                                    confirm="Supprimer cette condition ?"
                                    variant="ghost" icon="trash"
                        title="Supprimer"><span class="sr-only">Supprimer</span></x-confirm-form>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <form method="POST" action="{{ route('workflow.admin.workflows.transitions.conditions.store', [$workflow, $transition]) }}" class="border-t border-brand-border p-5">
                    @csrf
                    <p class="mb-3 text-[13px] font-medium text-slate-700">Ajouter une condition</p>
                    <div class="space-y-3">
                        <x-form-select name="form_field_id" label="Champ" :options="$availableFields" valueKey="form_field_id" labelKey="label" required />

                        <div>
                            <label for="operator" class="mb-1.5 block text-[13px] font-medium text-slate-700">Opérateur <span class="text-brand-danger">*</span></label>
                            <select id="operator" name="operator" required
                                    class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                                @foreach ($operatorLabels as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <x-form-input name="expected_value" label="Valeur attendue" hint="Ex. « 100000 »." />

                        <div>
                            <label for="logical_operator" class="mb-1.5 block text-[13px] font-medium text-slate-700">Combinée avec la condition suivante</label>
                            <select id="logical_operator" name="logical_operator"
                                    class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                                <option value="AND">ET (AND)</option>
                                <option value="OR">OU (OR)</option>
                            </select>
                        </div>
                    </div>
                    <x-button type="submit" size="sm" class="mt-3" icon="plus">Ajouter la condition</x-button>
                </form>
            @endif
        </x-card>
    </div>
@endsection
