@extends('layouts.admin', ['title' => $form->name])

@section('content')
    <x-page-header :title="$form->name" :description="$form->code . ' · ' . $form->displayVersion()">
        <x-slot:actions>
            @if ($form->isDraft())
                <x-confirm-form
                    :action="route('workflow.admin.forms.publish', $form)"
                    :confirm="'Publier « ' . ($form->name) . ' » ? Il deviendra utilisable pour créer des demandes et ne sera plus modifiable.'"
                    variant="primary" icon="check"
                >Publier</x-confirm-form>
            @endif
            <x-confirm-form
                :action="route('workflow.admin.forms.duplicate', $form)"
                :confirm="'Dupliquer « ' . ($form->name) . ' » en un nouveau formulaire indépendant (brouillon) ?'"
                variant="secondary" icon="file"
            >Dupliquer</x-confirm-form>
            @if (! $form->isArchived())
                <x-confirm-form
                    :action="route('workflow.admin.forms.archive', $form)"
                    :confirm="'Archiver « ' . ($form->name) . ' » ? Plus aucune nouvelle demande ne pourra en être créée.'"
                    variant="danger" icon="archive"
                >Archiver</x-confirm-form>
            @endif
            @if ($form->isDraft())
                <x-confirm-form
                    :action="route('workflow.admin.forms.destroy', $form)"
                    method="DELETE"
                    :confirm="'Supprimer définitivement « ' . ($form->name) . ' » ? Cette action est irréversible.'"
                    variant="danger" icon="trash"
                >Supprimer</x-confirm-form>
            @endif
            <x-button href="{{ route('workflow.admin.forms.index') }}" variant="secondary" icon="arrow-left">Retour</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4 flex items-center justify-between gap-3">
        <x-lifecycle-badge :status="$form->status" />
    </div>

    @unless ($form->isDraft())
        <div class="mb-6 flex items-start gap-2.5 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            @include('layouts.partials.icon', ['name' => 'alert', 'class' => 'mt-0.5 h-4 w-4 shrink-0'])
            <span>
                Ce formulaire est {{ $form->isPublished() ? 'publié' : 'archivé' }} et n'est donc plus modifiable.
                @if ($form->isPublished())
                    Utilisez « Dupliquer » pour repartir d'une copie éditable.
                @endif
            </span>
        </div>
    @endunless

    {{-- ==================================================
         CHAMPS — section principale, pleine largeur : c'est
         l'action la plus fréquente sur cet écran une fois le
         formulaire créé, elle ne doit pas se disputer l'attention
         avec "Informations" (déjà saisi à la création).
    =================================================== --}}
    <x-card class="mb-4" :padded="false">
        <div class="flex items-center justify-between border-b border-brand-border px-5 py-4">
            <div>
                <h2 class="text-[13px] font-semibold uppercase tracking-wide text-slate-500">
                    Champs ({{ $form->formFields->count() }})
                </h2>
                <p class="mt-0.5 text-xs text-slate-400">Composition du formulaire présenté au demandeur.</p>
            </div>
            @if ($form->isDraft())
                <x-button href="{{ route('workflow.admin.forms.fields.create', $form) }}" size="sm" icon="plus">Ajouter un champ</x-button>
            @endif
        </div>

        @if ($form->formFields->isEmpty())
            <x-empty-state icon="file" title="Aucun champ pour l'instant" description="Un formulaire doit contenir au moins un champ actif pour être publié.">
                @if ($form->isDraft())
                    <x-slot:actions>
                        <x-button href="{{ route('workflow.admin.forms.fields.create', $form) }}" size="sm" icon="plus">Ajouter un champ</x-button>
                    </x-slot:actions>
                @endif
            </x-empty-state>
        @else
            <ul class="divide-y divide-brand-border">
                @php $previousSection = null; @endphp
                @foreach ($form->formFields as $field)
                    @if ($field->section_title && $field->section_title !== $previousSection)
                        <li class="bg-slate-50/80 px-5 py-2 text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                            {{ $field->section_title }}
                        </li>
                    @endif
                    @php $previousSection = $field->section_title ?? $previousSection; @endphp
                    <li class="flex items-center gap-3 px-5 py-3">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-brand-navy">
                                {{ $field->label }}
                                @if ($field->is_required)
                                    <span class="text-brand-danger">*</span>
                                @endif
                            </p>
                            <p class="truncate text-xs text-slate-400">
                                {{ $field->technical_name }} · {{ $field->field_type }}
                                @if ($field->isSelect())
                                    · {{ $field->fieldOptions->count() }} option(s)
                                @endif
                            </p>
                        </div>

                        @if ($form->isDraft())
                            <div class="flex shrink-0 items-center gap-1">
                                <form method="POST" action="{{ route('workflow.admin.forms.fields.move-up', [$form, $field]) }}">
                                    @csrf
                                    <x-button type="submit" variant="ghost" size="sm" icon="chevron-down" class="rotate-180"
                        title="Monter"><span class="sr-only">Monter</span></x-button>
                                </form>
                                <form method="POST" action="{{ route('workflow.admin.forms.fields.move-down', [$form, $field]) }}">
                                    @csrf
                                    <x-button type="submit" variant="ghost" size="sm" icon="chevron-down"
                        title="Descendre"><span class="sr-only">Descendre</span></x-button>
                                </form>
                                <x-button href="{{ route('workflow.admin.forms.fields.edit', [$form, $field]) }}" variant="ghost" size="sm" icon="edit"
                        title="Modifier"><span class="sr-only">Modifier</span></x-button>
                                <x-confirm-form
                                    :action="route('workflow.admin.forms.fields.destroy', [$form, $field])"
                                    method="DELETE"
                                    :confirm="'Supprimer le champ « ' . ($field->label) . ' » ?'"
                                    variant="ghost" icon="trash"
                        title="Supprimer"><span class="sr-only">Supprimer</span></x-confirm-form>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($form->formFields->isNotEmpty() && $form->workflow)
            <div class="flex items-center justify-between gap-3 border-t border-brand-border bg-brand-blue/[0.04] px-5 py-3 text-[13px] text-brand-navy">
                <span>Prêt à définir les conditions des transitions du workflow « {{ $form->workflow->name }} » avec ces champs ?</span>
                @if ($form->workflow->workflowSteps->count() >= 2)
                    <x-button href="{{ route('workflow.admin.workflows.transitions.create', $form->workflow) }}" variant="secondary" size="sm" icon="branch">
                        Configurer les conditions
                    </x-button>
                @else
                    <x-button href="{{ route('workflow.admin.workflows.edit', $form->workflow) }}" variant="secondary" size="sm" icon="branch">
                        Ajouter les étapes du workflow d'abord
                    </x-button>
                @endif
            </div>
        @endif
    </x-card>

    {{-- ==================================================
         INFORMATIONS + URGENCE — secondaires : déjà saisies
         à la création, repliées par défaut pour ne pas
         reproposer un formulaire complet à chaque visite de
         cet écran ; "Modifier" les déplie à la demande.
    =================================================== --}}
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <details class="group lg:col-span-2 overflow-hidden rounded-xl border border-brand-border bg-white">
            <summary class="flex cursor-pointer list-none items-center justify-between px-5 py-4 [&::-webkit-details-marker]:hidden">
                <div>
                    <h2 class="text-[13px] font-semibold uppercase tracking-wide text-slate-500">Informations</h2>
                    <p class="mt-0.5 truncate text-xs text-slate-400">
                        {{ $form->formCategory?->name ?? '—' }} · {{ $form->workflow?->name ?? '—' }}
                    </p>
                </div>
                <span class="flex items-center gap-1.5 text-[13px] font-medium text-brand-blue">
                    Modifier
                    <svg viewBox="0 0 18 18" fill="none" class="h-3.5 w-3.5 transition-transform group-open:rotate-180">
                        <path d="M4.5 7l4.5 4.5L13.5 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </summary>

            <div class="border-t border-brand-border px-5 py-4">
                @if ($form->isDraft())
                    <form method="POST" action="{{ route('workflow.admin.forms.update', $form) }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @csrf
                        @method('PUT')
                        <x-form-input name="name" label="Nom" required :value="$form->name" />
                        <x-form-input name="code" label="Code" required :value="$form->code" />
                        <x-form-select name="form_category_id" label="Catégorie" :options="$formCategories" required :value="$form->form_category_id" />
                        <x-form-select name="workflow_id" label="Workflow" :options="$workflows" required :value="$form->workflow_id" />
                        <div class="sm:col-span-2">
                            <x-form-textarea name="description" label="Description" :value="$form->description" />
                        </div>
                        <div class="sm:col-span-2">
                            <x-button type="submit" size="sm" icon="check">Enregistrer</x-button>
                        </div>
                    </form>
                @else
                    <dl class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-xs text-slate-400">Catégorie</dt>
                            <dd class="mt-0.5 text-brand-navy">{{ $form->formCategory?->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-slate-400">Workflow</dt>
                            <dd class="mt-0.5 text-brand-navy">{{ $form->workflow?->name }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs text-slate-400">Description</dt>
                            <dd class="mt-0.5 text-brand-navy">{{ $form->description ?? '—' }}</dd>
                        </div>
                    </dl>
                @endif
            </div>
        </details>
    </div>
@endsection
