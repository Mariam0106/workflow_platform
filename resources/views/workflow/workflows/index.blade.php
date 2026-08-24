@extends('layouts.admin', ['title' => 'Workflows'])

@section('content')
    <x-page-header title="Workflows" description="{{ $workflows->total() }} workflow(s)">
        <x-slot:actions>
            <x-button href="{{ route('workflow.admin.workflow-categories.index') }}" variant="secondary" icon="layers">Catégories</x-button>
            <x-button href="{{ route('workflow.admin.workflows.create') }}" icon="plus">Nouveau workflow</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4">
        <x-search-input :value="$search" placeholder="Rechercher un workflow…" />
    </div>

    <x-card :padded="false">
        @if ($workflows->isEmpty())
            <x-empty-state icon="branch" title="Aucun workflow" description="Créez votre premier circuit de validation.">
                <x-slot:actions>
                    <x-button href="{{ route('workflow.admin.workflows.create') }}" icon="plus">Nouveau workflow</x-button>
                </x-slot:actions>
            </x-empty-state>
        @else
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-brand-border text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                        <th class="px-5 py-3">Workflow</th>
                        <th class="px-5 py-3">Catégorie</th>
                        <th class="px-5 py-3">Étapes</th>
                        <th class="px-5 py-3">Formulaires liés</th>
                        <th class="px-5 py-3">Statut</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border">
                    @foreach ($workflows as $workflow)
                        <tr class="transition hover:bg-slate-50/60">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('workflow.admin.workflows.edit', $workflow) }}" class="font-medium text-brand-navy hover:text-brand-blue">{{ $workflow->name }}</a>
                                <span class="block text-xs text-slate-400">{{ $workflow->code }} · {{ $workflow->displayVersion() }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $workflow->workflowCategory?->name }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $workflow->workflow_steps_count }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $workflow->forms_count }}</td>
                            <td class="px-5 py-3.5"><x-lifecycle-badge :status="$workflow->status" /></td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @if ($workflow->isDraft())
                                        <x-button href="{{ route('workflow.admin.workflows.edit', $workflow) }}" variant="ghost" size="sm" icon="edit"
                        title="Modifier"><span class="sr-only">Modifier</span></x-button>
                                        <x-confirm-form
                                            :action="route('workflow.admin.workflows.publish', $workflow)"
                                            :confirm="'Publier « ' . ($workflow->name) . ' » ? Il deviendra utilisable par des formulaires et ne sera plus modifiable.'"
                                            variant="ghost" icon="check"
                        title="Publier"><span class="sr-only">Publier</span></x-confirm-form>
                                    @endif
                                    <x-confirm-form
                                        :action="route('workflow.admin.workflows.duplicate', $workflow)"
                                        :confirm="'Dupliquer « ' . ($workflow->name) . ' » (étapes et transitions incluses) en un nouveau workflow indépendant ?'"
                                        variant="ghost" icon="file"
                        title="Dupliquer"><span class="sr-only">Dupliquer</span></x-confirm-form>
                                    @if (! $workflow->isArchived())
                                        <x-confirm-form
                                            :action="route('workflow.admin.workflows.archive', $workflow)"
                                            :confirm="'Archiver « ' . ($workflow->name) . ' » ? Il ne pourra plus être associé à de nouveaux formulaires.'"
                                            variant="ghost" icon="archive"
                        title="Archiver"><span class="sr-only">Archiver</span></x-confirm-form>
                                    @endif
                                    @if ($workflow->isDraft())
                                        <x-confirm-form
                                            :action="route('workflow.admin.workflows.destroy', $workflow)"
                                            method="DELETE"
                                            :confirm="'Supprimer définitivement « ' . ($workflow->name) . ' » ? Cette action est irréversible.'"
                                            variant="ghost" icon="trash"
                        title="Supprimer"><span class="sr-only">Supprimer</span></x-confirm-form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        <x-simple-paginator :paginator="$workflows" />
    </x-card>
@endsection
