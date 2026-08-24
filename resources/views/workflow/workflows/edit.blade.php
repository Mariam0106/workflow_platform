@extends('layouts.admin', ['title' => $workflow->name])

@section('content')
    <x-page-header :title="$workflow->name" :description="$workflow->code . ' · ' . $workflow->displayVersion()">
        <x-slot:actions>
            @if ($workflow->isDraft())
                <x-confirm-form
                    :action="route('workflow.admin.workflows.publish', $workflow)"
                    :confirm="'Publier « ' . ($workflow->name) . ' » ? Il deviendra utilisable par des formulaires et ne sera plus modifiable.'"
                    variant="primary" icon="check"
                >Publier</x-confirm-form>
            @endif
            <x-confirm-form
                :action="route('workflow.admin.workflows.duplicate', $workflow)"
                :confirm="'Dupliquer « ' . ($workflow->name) . ' » (étapes et transitions incluses) en un nouveau workflow indépendant ?'"
                variant="secondary" icon="file"
            >Dupliquer</x-confirm-form>
            @if (! $workflow->isArchived())
                <x-confirm-form
                    :action="route('workflow.admin.workflows.archive', $workflow)"
                    :confirm="'Archiver « ' . ($workflow->name) . ' » ? Il ne pourra plus être associé à de nouveaux formulaires.'"
                    variant="danger" icon="archive"
                >Archiver</x-confirm-form>
            @endif
            @if ($workflow->isDraft())
                <x-confirm-form
                    :action="route('workflow.admin.workflows.destroy', $workflow)"
                    method="DELETE"
                    :confirm="'Supprimer définitivement « ' . ($workflow->name) . ' » ? Cette action est irréversible.'"
                    variant="danger" icon="trash"
                >Supprimer</x-confirm-form>
            @endif
            <x-button href="{{ route('workflow.admin.workflows.index') }}" variant="secondary" icon="arrow-left">Retour</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4">
        <x-lifecycle-badge :status="$workflow->status" />
    </div>

    @unless ($workflow->isDraft())
        <div class="mb-6 flex items-start gap-2.5 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            @include('layouts.partials.icon', ['name' => 'alert', 'class' => 'mt-0.5 h-4 w-4 shrink-0'])
            <span>
                Ce workflow est {{ $workflow->isPublished() ? 'publié' : 'archivé' }} et n'est donc plus modifiable.
                @if ($workflow->isPublished())
                    Utilisez « Dupliquer » pour repartir d'une copie éditable.
                @endif
            </span>
        </div>
    @endunless

    {{-- ==================================================
         FORMULAIRES LIÉS — pour boucler la boucle de création
         (Workflow → Formulaire → Conditions → Publier) : une fois
         ce Workflow publié, il faut aussi penser à publier son/ses
         Formulaire(s), sans quoi personne ne peut réellement
         l'utiliser (BR-15/30).
    =================================================== --}}
    @if ($workflow->forms->isNotEmpty())
        <x-card class="mb-4" :padded="false">
            <div class="border-b border-brand-border px-5 py-4">
                <h2 class="text-[13px] font-semibold uppercase tracking-wide text-slate-500">Formulaire(s) lié(s) à ce workflow</h2>
            </div>
            <ul class="divide-y divide-brand-border">
                @foreach ($workflow->forms as $linkedForm)
                    <li class="flex items-center gap-3 px-5 py-3">
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('workflow.admin.forms.edit', $linkedForm) }}" class="truncate text-sm font-medium text-brand-navy hover:text-brand-blue">
                                {{ $linkedForm->name }}
                            </a>
                            <p class="truncate text-xs text-slate-400">{{ $linkedForm->code }} · {{ $linkedForm->formFields()->count() }} champ(s)</p>
                        </div>
                        <x-lifecycle-badge :status="$linkedForm->status" />
                        @if ($workflow->isPublished() && $linkedForm->isDraft())
                            <x-confirm-form
                                :action="route('workflow.admin.forms.publish', $linkedForm)"
                                :confirm="'Publier « ' . $linkedForm->name . ' » ? Il deviendra utilisable pour créer des demandes.'"
                                variant="primary" icon="check"
                            >Publier</x-confirm-form>
                        @endif
                    </li>
                @endforeach
            </ul>
            @if ($workflow->isPublished() && $workflow->forms->contains(fn ($f) => $f->isDraft()))
                <div class="flex items-start gap-2.5 border-t border-brand-border bg-amber-50/60 px-5 py-3 text-[13px] text-amber-800">
                    @include('layouts.partials.icon', ['name' => 'alert', 'class' => 'mt-0.5 h-4 w-4 shrink-0'])
                    <span>Ce workflow est publié, mais au moins un formulaire ci-dessus est encore en brouillon - publie-le aussi pour qu'il soit réellement utilisable.</span>
                </div>
            @endif
        </x-card>
    @endif

    {{-- ==================================================
         ÉTAPES + TRANSITIONS — section principale, pleine
         largeur : c'est ce qu'on vient configurer en priorité
         sur cet écran, une fois le workflow créé.
    =================================================== --}}
    <div class="space-y-4">
        <x-card :padded="false">
            <div class="flex items-center justify-between border-b border-brand-border px-5 py-4">
                <h2 class="text-[13px] font-semibold uppercase tracking-wide text-slate-500">
                    Étapes ({{ $workflow->workflowSteps->count() }})
                </h2>
                @if ($workflow->isDraft())
                    <x-button href="{{ route('workflow.admin.workflows.steps.create', $workflow) }}" size="sm" icon="plus">Ajouter une étape</x-button>
                @endif
            </div>

            @if ($workflow->workflowSteps->isEmpty())
                <x-empty-state icon="branch" title="Aucune étape" description="Un workflow doit contenir au moins une étape, dont une de début et une de fin." />
            @else
                @if ($workflow->isDraft())
                    <p class="border-b border-brand-border bg-slate-50/60 px-5 py-2 text-xs text-slate-500">
                        Glissez une étape par sa poignée <span class="inline-block px-0.5">⠿</span> pour la réordonner.
                    </p>
                @endif
                <ul id="workflow-steps-list" class="divide-y divide-brand-border">
                    @foreach ($workflow->workflowSteps as $step)
                        <li class="flex items-center gap-3 px-5 py-3 {{ $workflow->isDraft() ? 'cursor-grab active:cursor-grabbing' : '' }}"
                            @if ($workflow->isDraft()) draggable="true" data-step-id="{{ $step->id }}" @endif>
                            @if ($workflow->isDraft())
                                <span class="shrink-0 select-none text-slate-300" title="Glisser pour réordonner">⠿⠿</span>
                            @endif
                            <div class="min-w-0 flex-1">
                                <p class="flex items-center gap-1.5 truncate text-sm font-medium text-brand-navy">
                                    {{ $step->step_order }}. {{ $step->name }}
                                    @if ($step->is_start) <x-badge tone="blue">Début</x-badge> @endif
                                    @if ($step->is_end) <x-badge tone="success">Fin</x-badge> @endif
                                </p>
                                <p class="truncate text-xs text-slate-400">
                                    {{ $step->code }} ·
                                    @switch($step->validator_type->value)
                                        @case('ROLE') Validateur : Rôle Applicatif @break
                                        @case('BUSINESS_FUNCTION') Validateur : Fonction Métier @break
                                        @case('USER') Validateur : Utilisateur désigné @break
                                        @case('N_PLUS_1') Validateur : Responsable direct (N+1) @break
                                        @case('ENTITY_MANAGER') Validateur : Responsable d'Entité @break
                                        @case('DEPARTMENT_MANAGER') Validateur : Responsable de Département @break
                                    @endswitch
                                    @if ($step->validatorReferenceLabel())
                                        — {{ $step->validatorReferenceLabel() }}
                                    @endif
                                </p>
                            </div>

                            @if ($workflow->isDraft())
                                <div class="flex shrink-0 items-center gap-1">
                                    @unless ($step->is_start)
                                        <form method="POST" action="{{ route('workflow.admin.workflows.steps.set-start', [$workflow, $step]) }}">
                                            @csrf
                                            <x-button type="submit" variant="ghost" size="sm">Définir début</x-button>
                                        </form>
                                    @endunless
                                    <form method="POST" action="{{ route('workflow.admin.workflows.steps.move-up', [$workflow, $step]) }}">
                                        @csrf
                                        <x-button type="submit" variant="ghost" size="sm" icon="chevron-down" class="rotate-180"
                        title="Monter"><span class="sr-only">Monter</span></x-button>
                                    </form>
                                    <form method="POST" action="{{ route('workflow.admin.workflows.steps.move-down', [$workflow, $step]) }}">
                                        @csrf
                                        <x-button type="submit" variant="ghost" size="sm" icon="chevron-down"
                        title="Descendre"><span class="sr-only">Descendre</span></x-button>
                                    </form>
                                    <x-button href="{{ route('workflow.admin.workflows.steps.edit', [$workflow, $step]) }}" variant="ghost" size="sm" icon="edit"
                        title="Modifier"><span class="sr-only">Modifier</span></x-button>
                                    <x-confirm-form
                                        :action="route('workflow.admin.workflows.steps.destroy', [$workflow, $step])"
                                        method="DELETE"
                                        :confirm="'Supprimer l\'étape « ' . ($step->name) . ' » ?'"
                                        variant="ghost" icon="trash"
                        title="Supprimer"><span class="sr-only">Supprimer</span></x-confirm-form>
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>

        {{-- ==================================================
             TRANSITIONS
        =================================================== --}}
        <x-card :padded="false">
            <div class="flex items-center justify-between border-b border-brand-border px-5 py-4">
                <h2 class="text-[13px] font-semibold uppercase tracking-wide text-slate-500">
                    Transitions ({{ $workflow->workflowSteps->sum(fn ($s) => $s->outgoingTransitions->count()) }})
                </h2>
                @if ($workflow->isDraft())
                    @if ($workflow->workflowSteps->count() < 2)
                        <span class="text-xs text-slate-400">Ajoutez au moins 2 étapes pour créer une transition.</span>
                    @else
                        <x-button href="{{ route('workflow.admin.workflows.transitions.create', $workflow) }}" size="sm" icon="plus">Ajouter une transition</x-button>
                    @endif
                @endif
            </div>

            @if ($workflow->forms_count === 0)
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-brand-border bg-amber-50/60 px-5 py-3 text-[13px] text-amber-800">
                    <span>
                        Les conditions d'une transition se basent sur les champs d'un Formulaire - aucun Formulaire n'utilise encore ce Workflow.
                    </span>
                    <div class="flex shrink-0 items-center gap-2">
                        <x-button href="{{ route('workflow.admin.forms.create', ['workflow' => $workflow->id]) }}" size="sm" variant="secondary" icon="plus">
                            Créer un formulaire pour ce workflow
                        </x-button>
                        @if ($existingForms->isNotEmpty())
                            <button type="button" onclick="document.getElementById('form-from-existing-panel').classList.toggle('hidden')"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl px-3 py-1.5 text-[13px] font-semibold text-brand-navy underline decoration-dotted underline-offset-4 transition hover:text-brand-blue">
                                ou repartir d'un formulaire existant
                            </button>
                        @endif
                    </div>
                </div>

                @if ($existingForms->isNotEmpty())
                    {{-- ==================================================
                         BR-11/12 : ceci crée toujours un NOUVEAU
                         Formulaire (copie de la structure) plutôt que de
                         réassigner l'existant - un Formulaire garde
                         toujours exactement un seul Workflow ; c'est le
                         Workflow qui peut être réutilisé par plusieurs
                         Formulaires, jamais l'inverse (voir
                         FormService::duplicateForWorkflow()).
                    =================================================== --}}
                    <form id="form-from-existing-panel" method="POST" action="{{ route('workflow.admin.workflows.forms-from-existing', $workflow) }}"
                          class="hidden space-y-3 border-b border-brand-border bg-slate-50/60 px-5 py-4">
                        @csrf
                        <x-form-select name="source_form_id" label="Formulaire à reprendre" :options="$existingForms" placeholder="—" required
                                        hint="Ses champs seront copiés dans un nouveau formulaire, dédié à ce workflow - le formulaire d'origine garde son propre workflow, inchangé." />
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <x-form-input name="name" label="Nom du nouveau formulaire" required />
                            <x-form-input name="code" label="Code du nouveau formulaire" required hint="Doit être différent du formulaire d'origine." />
                        </div>
                        <x-button type="submit" size="sm" icon="plus">Créer ce formulaire</x-button>
                    </form>
                @endif
            @endif

            @php
                $allTransitions = $workflow->workflowSteps->flatMap(fn ($s) => $s->outgoingTransitions);
            @endphp

            @if ($allTransitions->isEmpty())
                <x-empty-state icon="branch" title="Aucune transition" description="Les transitions relient les étapes entre elles et déterminent le chemin suivi par une demande." />
            @else
                <ul class="divide-y divide-brand-border">
                    @foreach ($allTransitions as $transition)
                        <li class="flex items-center gap-3 px-5 py-3">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-brand-navy">
                                    {{ $transition->fromStep?->name }} → {{ $transition->toStep?->name }}
                                    @if ($transition->is_default) <x-badge tone="slate">Par défaut</x-badge> @endif
                                </p>
                                <p class="truncate text-xs text-slate-400">
                                    {{ $transition->action_name }} ·
                                    @switch($transition->priority)
                                        @case(80) Priorité élevée @break
                                        @case(100) Priorité urgente @break
                                        @default Priorité normale
                                    @endswitch
                                    @if ($transition->transitionConditions->isNotEmpty())
                                        · {{ $transition->transitionConditions->count() }} condition(s)
                                    @endif
                                </p>
                            </div>
                            @if ($workflow->isDraft())
                                <div class="flex shrink-0 items-center gap-1">
                                    <x-button href="{{ route('workflow.admin.workflows.transitions.edit', [$workflow, $transition]) }}" variant="ghost" size="sm" icon="edit"
                        title="Modifier"><span class="sr-only">Modifier</span></x-button>
                                    <x-confirm-form
                                        :action="route('workflow.admin.workflows.transitions.destroy', [$workflow, $transition])"
                                        method="DELETE"
                                        confirm="Supprimer cette transition ?"
                                        variant="ghost" icon="trash"
                        title="Supprimer"><span class="sr-only">Supprimer</span></x-confirm-form>
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>
    </div>

    {{-- ==================================================
         NOTIFICATIONS DE CLÔTURE — qui prévenir (en plus du
         Demandeur, toujours notifié) quand une Demande de ce
         Workflow est entièrement approuvée. Utile pour un
         exécutant réel de l'action (ex. Crédit Client qui va
         effectivement ouvrir le compte) sans être Validateur
         d'aucune Étape.
    =================================================== --}}
    <x-card class="mt-4" :padded="false">
        <div class="border-b border-brand-border px-5 py-4">
            <h2 class="text-[13px] font-semibold uppercase tracking-wide text-slate-500">Notifications de clôture</h2>
            <p class="mt-0.5 text-xs text-slate-400">Le Demandeur est toujours prévenu automatiquement. Ajoute ici qui doit l'être en plus.</p>
        </div>

        @if ($workflow->completionNotifications->isNotEmpty())
            <ul class="divide-y divide-brand-border">
                @foreach ($workflow->completionNotifications as $completionNotification)
                    <li class="flex items-center gap-3 px-5 py-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                            @include('layouts.partials.icon', ['name' => $completionNotification->isBusinessFunction() ? 'briefcase' : 'users', 'class' => 'h-3.5 w-3.5'])
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-brand-navy">{{ $completionNotification->referenceLabel() ?? '—' }}</p>
                            <p class="text-xs text-slate-400">{{ $completionNotification->isBusinessFunction() ? 'Fonction Métier' : 'Utilisateur désigné' }}</p>
                        </div>
                        <x-confirm-form
                            :action="route('workflow.admin.workflows.completion-notifications.destroy', [$workflow, $completionNotification])"
                            method="DELETE"
                            :confirm="'Retirer ' . ($completionNotification->referenceLabel() ?? 'ce destinataire') . ' des notifications de clôture ?'"
                            variant="ghost" icon="trash"
                        title="Retirer"><span class="sr-only">Retirer</span></x-confirm-form>
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($workflow->isDraft() || $workflow->isPublished())
            <form method="POST" action="{{ route('workflow.admin.workflows.completion-notifications.store', $workflow) }}" class="flex flex-wrap items-end gap-3 border-t border-brand-border p-5">
                @csrf
                <div class="min-w-[10rem]">
                    <label for="notify_type" class="mb-1.5 block text-[13px] font-medium text-slate-700">Type</label>
                    <select id="notify_type" name="notify_type" onchange="updateNotifyReferenceFields()"
                            class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                        <option value="BUSINESS_FUNCTION">Fonction Métier</option>
                        <option value="USER">Utilisateur désigné</option>
                    </select>
                </div>
                <div id="notify-ref-business-function" class="min-w-[12rem] flex-1">
                    <x-form-select name="notify_reference" label="Fonction Métier" :options="$businessFunctions" placeholder="—" />
                </div>
                <div id="notify-ref-user" class="hidden min-w-[12rem] flex-1">
                    <x-user-picker name="notify_reference" label="Utilisateur" :users="$users" :entities="$entities" :departments="$departments" />
                </div>
                <script>
                    function updateNotifyReferenceFields() {
                        var isBiz = document.getElementById('notify_type').value === 'BUSINESS_FUNCTION';
                        var bizWrap = document.getElementById('notify-ref-business-function');
                        var userWrap = document.getElementById('notify-ref-user');
                        bizWrap.classList.toggle('hidden', !isBiz);
                        bizWrap.querySelector('select').disabled = !isBiz;
                        userWrap.classList.toggle('hidden', isBiz);
                        userWrap.querySelector('input[data-role="value"]').disabled = isBiz;
                    }
                    updateNotifyReferenceFields();
                </script>
                <x-button type="submit" size="sm" icon="plus">Ajouter</x-button>
            </form>
        @endif
    </x-card>

    {{-- ==================================================
         INFORMATIONS — secondaire : déjà saisie à la création,
         repliée par défaut ; "Modifier" la déplie à la demande.
    =================================================== --}}
    <details class="group mt-4 overflow-hidden rounded-xl border border-brand-border bg-white">
        <summary class="flex cursor-pointer list-none items-center justify-between px-5 py-4 [&::-webkit-details-marker]:hidden">
            <div>
                <h2 class="text-[13px] font-semibold uppercase tracking-wide text-slate-500">Informations</h2>
                <p class="mt-0.5 truncate text-xs text-slate-400">{{ $workflow->workflowCategory?->name ?? '—' }}</p>
            </div>
            <span class="flex items-center gap-1.5 text-[13px] font-medium text-brand-blue">
                Modifier
                <svg viewBox="0 0 18 18" fill="none" class="h-3.5 w-3.5 transition-transform group-open:rotate-180">
                    <path d="M4.5 7l4.5 4.5L13.5 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        </summary>

        <div class="border-t border-brand-border px-5 py-4">
            @if ($workflow->isDraft())
                <form method="POST" action="{{ route('workflow.admin.workflows.update', $workflow) }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @csrf
                    @method('PUT')
                    <x-form-input name="name" label="Nom" required :value="$workflow->name" />
                    <x-form-input name="code" label="Code" required :value="$workflow->code" />
                    <x-form-select name="workflow_category_id" label="Catégorie" :options="$workflowCategories" required :value="$workflow->workflow_category_id" />
                    <div class="sm:col-span-2">
                        <x-form-textarea name="description" label="Description" :value="$workflow->description" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-button type="submit" size="sm" icon="check">Enregistrer</x-button>
                    </div>
                </form>
            @else
                <dl class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-xs text-slate-400">Catégorie</dt>
                        <dd class="mt-0.5 text-brand-navy">{{ $workflow->workflowCategory?->name }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs text-slate-400">Description</dt>
                        <dd class="mt-0.5 text-brand-navy">{{ $workflow->description ?? '—' }}</dd>
                    </div>
                </dl>
            @endif
        </div>
    </details>

    @if ($workflow->isDraft() && $workflow->workflowSteps->count() > 1)
        <form id="reorder-steps-form" method="POST" action="{{ route('workflow.admin.workflows.steps.reorder', $workflow) }}" class="hidden">
            @csrf
            <input type="hidden" name="ordered_ids" id="reorder-steps-input">
        </form>
        <script>
            (function () {
                var list = document.getElementById('workflow-steps-list');
                if (!list) return;
                var dragged = null;

                list.querySelectorAll('li[draggable="true"]').forEach(function (li) {
                    li.addEventListener('dragstart', function () {
                        dragged = li;
                        li.classList.add('opacity-40');
                    });
                    li.addEventListener('dragend', function () {
                        li.classList.remove('opacity-40');
                    });
                    li.addEventListener('dragover', function (event) {
                        event.preventDefault();
                    });
                    li.addEventListener('drop', function (event) {
                        event.preventDefault();
                        if (!dragged || dragged === li) return;

                        var rect = li.getBoundingClientRect();
                        var insertBefore = (event.clientY - rect.top) < rect.height / 2;
                        list.insertBefore(dragged, insertBefore ? li : li.nextSibling);

                        var ids = Array.prototype.map.call(
                            list.querySelectorAll('li[draggable="true"]'),
                            function (item) { return item.dataset.stepId; }
                        );
                        document.getElementById('reorder-steps-input').value = ids.join(',');
                        document.getElementById('reorder-steps-form').submit();
                    });
                });
            })();
        </script>
    @endif
@endsection
