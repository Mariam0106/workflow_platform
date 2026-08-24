@extends('layouts.admin', ['title' => 'Nouvelle transition'])

@section('content')
    <x-page-header title="Nouvelle transition" :description="'Workflow « ' . $workflow->name . ' »'">
        <x-slot:actions>
            <x-button href="{{ route('workflow.admin.workflows.edit', $workflow) }}" variant="secondary" icon="arrow-left">Retour</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('workflow.admin.workflows.transitions.store', $workflow) }}" class="max-w-xl space-y-4">
        @csrf
        <x-card>
            <div class="space-y-4">
                <x-form-select name="from_step_id" label="Depuis l'étape" :options="$workflow->workflowSteps" required
                                labelKey="name" hint="L'étape de départ de cette transition." />
                <x-form-select name="to_step_id" label="Vers l'étape" :options="$workflow->workflowSteps" required
                                labelKey="name" hint="L'étape atteinte si cette transition est exécutée." />
                <x-form-input name="action_name" label="Nom de l'action" required hint="Ex. « send_to_finance », « escalate_to_compliance »." />
                <div>
                    <label for="priority" class="mb-1.5 block text-[13px] font-medium text-slate-700">Priorité <span class="text-brand-danger">*</span></label>
                    <select id="priority" name="priority" required
                            class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                        <option value="50" @selected(old('priority', 50) == 50)>Normale</option>
                        <option value="80" @selected(old('priority', 50) == 80)>Élevée</option>
                        <option value="100" @selected(old('priority', 50) == 100)>Haute</option>
                    </select>
                    <p class="mt-1 text-xs text-slate-400">En cas de conflit entre plusieurs transitions possibles, la plus prioritaire est retenue.</p>
                    @error('priority') <p class="mt-1 text-xs text-brand-danger">{{ $message }}</p> @enderror
                </div>
                <x-form-textarea name="description" label="Description" />
                <x-form-checkbox name="is_default" label="Transition par défaut"
                                  hint="Exécutée si aucune autre transition avec conditions n'est retenue." />
            </div>
        </x-card>

        <div class="flex items-center gap-2.5">
            <x-button type="submit" icon="plus">Créer et ajouter des conditions</x-button>
            <x-button href="{{ route('workflow.admin.workflows.edit', $workflow) }}" variant="secondary">Annuler</x-button>
        </div>
    </form>
@endsection
