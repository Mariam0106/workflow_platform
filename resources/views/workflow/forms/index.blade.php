@extends('layouts.admin', ['title' => 'Formulaires'])

@section('content')
    <x-page-header title="Formulaires" description="{{ $forms->total() }} formulaire(s)">
        <x-slot:actions>
            <x-button href="{{ route('workflow.admin.form-categories.index') }}" variant="secondary" icon="layers">Catégories</x-button>
            <x-button href="{{ route('workflow.admin.forms.create') }}" icon="plus">Nouveau formulaire</x-button>
        </x-slot:actions>
    </x-page-header>

    @if ($filteredWorkflow)
        <div class="mb-4 flex items-center justify-between gap-3 rounded-lg border border-brand-blue/30 bg-brand-blue/[0.06] px-4 py-3 text-[13px] text-brand-navy">
            <span>Formulaire(s) du workflow « {{ $filteredWorkflow->name }} » — pense à publier ceux encore en brouillon pour qu'ils soient utilisables.</span>
            <a href="{{ route('workflow.admin.forms.index') }}" class="shrink-0 font-medium text-brand-blue hover:text-brand-blue-dark">Voir tous les formulaires</a>
        </div>
    @endif

    <div class="mb-4">
        <x-search-input :value="$search" placeholder="Rechercher un formulaire…" />
    </div>

    <x-card :padded="false">
        @if ($forms->isEmpty())
            <x-empty-state icon="file" title="Aucun formulaire" description="Créez votre premier formulaire dynamique et rattachez-le à un Workflow.">
                <x-slot:actions>
                    <x-button href="{{ route('workflow.admin.forms.create') }}" icon="plus">Nouveau formulaire</x-button>
                </x-slot:actions>
            </x-empty-state>
        @else
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-brand-border text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                        <th class="px-5 py-3">Formulaire</th>
                        <th class="px-5 py-3">Catégorie</th>
                        <th class="px-5 py-3">Workflow</th>
                        <th class="px-5 py-3">Champs</th>
                        <th class="px-5 py-3">Statut</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border">
                    @foreach ($forms as $form)
                        <tr class="transition hover:bg-slate-50/60">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('workflow.admin.forms.edit', $form) }}" class="font-medium text-brand-navy hover:text-brand-blue">{{ $form->name }}</a>
                                <span class="block text-xs text-slate-400">{{ $form->code }} · {{ $form->displayVersion() }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $form->formCategory?->name }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $form->workflow?->name }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $form->form_fields_count }}</td>
                            <td class="px-5 py-3.5"><x-lifecycle-badge :status="$form->status" /></td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @if ($form->isDraft())
                                        <x-button href="{{ route('workflow.admin.forms.edit', $form) }}" variant="ghost" size="sm" icon="edit"
                        title="Modifier"><span class="sr-only">Modifier</span></x-button>
                                        <x-confirm-form
                                            :action="route('workflow.admin.forms.publish', $form)"
                                            :confirm="'Publier « ' . ($form->name) . ' » ? Il deviendra utilisable pour créer des demandes et ne sera plus modifiable.'"
                                            variant="ghost" icon="check"
                        title="Publier"><span class="sr-only">Publier</span></x-confirm-form>
                                    @endif
                                    <x-confirm-form
                                        :action="route('workflow.admin.forms.duplicate', $form)"
                                        :confirm="'Dupliquer « ' . ($form->name) . ' » en un nouveau formulaire indépendant (brouillon) ?'"
                                        variant="ghost" icon="file"
                        title="Dupliquer"><span class="sr-only">Dupliquer</span></x-confirm-form>
                                    @if (! $form->isArchived())
                                        <x-confirm-form
                                            :action="route('workflow.admin.forms.archive', $form)"
                                            :confirm="'Archiver « ' . ($form->name) . ' » ? Plus aucune nouvelle demande ne pourra en être créée.'"
                                            variant="ghost" icon="archive"
                        title="Archiver"><span class="sr-only">Archiver</span></x-confirm-form>
                                    @endif
                                    @if ($form->isDraft())
                                        <x-confirm-form
                                            :action="route('workflow.admin.forms.destroy', $form)"
                                            method="DELETE"
                                            :confirm="'Supprimer définitivement « ' . ($form->name) . ' » ? Cette action est irréversible.'"
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
        <x-simple-paginator :paginator="$forms" />
    </x-card>
@endsection
