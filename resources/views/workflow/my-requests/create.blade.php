@extends('layouts.admin', ['title' => $form->name])

@section('content')
    <x-page-header :title="$form->name" :description="$form->description">
        <x-slot:actions>
            <x-button href="{{ route('workflow.my-requests.select-form') }}" variant="secondary" icon="arrow-left">Retour</x-button>
        </x-slot:actions>
    </x-page-header>

    @if ($draft && $draftValues->isNotEmpty())
        <div class="mb-4 flex items-center gap-2.5 rounded-lg border border-brand-blue/20 bg-brand-blue/[0.04] px-4 py-3 text-[13px] text-brand-navy">
            @include('layouts.partials.icon', ['name' => 'clock', 'class' => 'h-4 w-4 shrink-0 text-brand-blue'])
            <span>Brouillon repris — dernière sauvegarde automatique à {{ $draft->updated_at->format('H:i') }}.</span>
        </div>
    @endif

    <form method="POST" action="{{ route('workflow.my-requests.store', $form) }}" id="request-form" enctype="multipart/form-data" class="max-w-2xl space-y-4">
        @csrf
        <input type="hidden" name="form_id" value="{{ $form->id }}">

        <x-card :padded="false">
            <div id="wizard-progress" class="hidden items-center justify-between border-b border-brand-border px-6 py-3.5 text-[13px] text-slate-500">
                <span id="wizard-progress-label"></span>
                <div id="wizard-progress-dots" class="flex items-center gap-1.5"></div>
            </div>

            <div class="p-6">
                @php
                    $previousSection = null;
                    $pageIndex = -1;
                    $pageTitles = [];
                @endphp
                @foreach ($form->formFields as $field)
                    @php
                        $inputName = "values[{$field->id}]";
                        $oldKey = "values.{$field->id}";
                        $draftValue = $draftValues->get($field->id);
                        $startsNewPage = $field->section_title && $field->section_title !== $previousSection;
                    @endphp

                    @if ($startsNewPage)
                        @if ($pageIndex >= 0) </div> @endif
                        @php $pageIndex++; $pageTitles[] = $field->section_title; @endphp
                        <div class="wizard-page space-y-4" data-page="{{ $pageIndex }}" @if ($pageIndex > 0) style="display:none" @endif>
                    @elseif ($pageIndex === -1)
                        @php $pageIndex = 0; $pageTitles[] = null; @endphp
                        <div class="wizard-page space-y-4" data-page="0">
                    @endif
                    @php $previousSection = $field->section_title ?? $previousSection; @endphp

                    @if ($field->isFile())
                        <div>
                            <label for="{{ $inputName }}" class="mb-1.5 block text-[13px] font-medium text-slate-700">
                                {{ $field->label }} @if ($field->is_required) <span class="text-brand-danger">*</span> @endif
                            </label>
                            <input id="{{ $inputName }}" type="file" name="{{ $inputName }}" @if ($field->is_required) required @endif
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx"
                                   class="block w-full rounded-lg border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-[13px] file:font-medium file:text-brand-navy hover:file:bg-slate-200 focus:outline-none focus:ring-4 focus:ring-brand-blue/10 {{ $errors->has($oldKey) ? 'border-red-300 focus:border-red-400 focus:ring-red-100' : 'border-brand-border focus:border-brand-blue focus:ring-brand-blue/10' }}">
                            <p class="mt-1 text-xs text-slate-400">PDF, image, Word ou Excel — 10 Mo max. Envoyé avec la demande (pas sauvegardé dans le brouillon automatique).</p>
                            @error($oldKey) <p class="mt-1 text-xs text-brand-danger">{{ $message }}</p> @enderror
                        </div>
                    @elseif ($field->isSelect())
                        @php
                            $freeTextOption = $field->fieldOptions->first(fn ($o) => $o->isFreeText());
                            $realValues = $field->fieldOptions->reject(fn ($o) => $o->isFreeText())->pluck('value');
                            $defaultValue = optional($field->fieldOptions->firstWhere('is_default', true))->value;
                            $currentValue = old($oldKey, $draftValue ?? $defaultValue);
                            $isOther = $freeTextOption && (
                                $currentValue === \App\Models\FieldOption::FREE_TEXT_VALUE
                                || ($currentValue !== null && ! $realValues->contains($currentValue))
                            );
                            $selectValue = $isOther ? \App\Models\FieldOption::FREE_TEXT_VALUE : $currentValue;
                            $freeTextValue = $isOther && $currentValue !== \App\Models\FieldOption::FREE_TEXT_VALUE ? $currentValue : '';
                        @endphp
                        <div>
                            <x-form-select :name="$inputName" :label="$field->label" :required="$field->is_required"
                                            :options="$field->fieldOptions" valueKey="value" labelKey="label"
                                            :value="$selectValue" :error-key="$oldKey"
                                            :data-other-select="$freeTextOption ? $inputName : null" />

                            @if ($freeTextOption)
                                <input type="text" name="{{ $inputName }}" value="{{ $freeTextValue }}"
                                       placeholder="Précisez…" data-other-input="{{ $inputName }}"
                                       {{ $isOther ? '' : 'disabled' }}
                                       class="mt-2 block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10 {{ $isOther ? '' : 'hidden' }}">
                            @endif
                        </div>
                    @elseif ($field->isDate())
                        <x-form-input :name="$inputName" type="date" :label="$field->label" :required="$field->is_required" :value="old($oldKey, $draftValue ?? $field->default_value)" :error-key="$oldKey" />
                    @elseif ($field->isMontant())
                        @php
                            $rawMontant = old($oldKey, $draftValue ?? $field->default_value);
                            $displayMontant = $rawMontant !== null && $rawMontant !== ''
                                ? number_format((float) str_replace(',', '.', (string) $rawMontant), 2, ',', ' ')
                                : '';
                        @endphp
                        <div>
                            <label for="{{ $inputName }}_display" class="mb-1.5 block text-[13px] font-medium text-slate-700">
                                {{ $field->label }} @if ($field->is_required) <span class="text-brand-danger">*</span> @endif
                            </label>
                            <div class="relative">
                                <input type="text" id="{{ $inputName }}_display" inputmode="decimal" autocomplete="off"
                                       value="{{ $displayMontant }}" placeholder="0,00"
                                       data-montant-display="{{ $inputName }}"
                                       @if ($field->is_required) required @endif
                                       class="block w-full rounded-lg border bg-white px-3.5 py-2.5 pr-14 text-sm text-brand-navy shadow-sm transition focus:outline-none focus:ring-4 {{ $errors->has($oldKey) ? 'border-red-300 focus:border-red-400 focus:ring-red-100' : 'border-brand-border focus:border-brand-blue focus:ring-brand-blue/10' }}">
                                <span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400">MAD</span>
                            </div>
                            <input type="hidden" name="{{ $inputName }}" data-montant-value="{{ $inputName }}"
                                   value="{{ $rawMontant !== null ? str_replace(',', '.', (string) $rawMontant) : '' }}">
                            @if ($field->placeholder)
                                <p class="mt-1 text-xs text-slate-400">{{ $field->placeholder }}</p>
                            @endif
                            @error($oldKey) <p class="mt-1 text-xs text-brand-danger">{{ $message }}</p> @enderror
                        </div>
                    @elseif ($field->isNumber())
                        <x-form-input :name="$inputName" type="number" :label="$field->label" :required="$field->is_required"
                                       :value="old($oldKey, $draftValue ?? $field->default_value)" :error-key="$oldKey" />
                    @elseif ($field->field_type === 'textarea')
                        <x-form-textarea :name="$inputName" :label="$field->label" :required="$field->is_required" :value="old($oldKey, $draftValue ?? $field->default_value)" :error-key="$oldKey" />
                    @else
                        <x-form-input :name="$inputName" :type="$field->field_type" :label="$field->label" :required="$field->is_required"
                                       :value="old($oldKey, $draftValue ?? $field->default_value)" :placeholder="$field->placeholder" :error-key="$oldKey" />
                    @endif
                @endforeach
                @if ($pageIndex >= 0) </div> @endif
            </div>

            @if ($pageIndex > 0)
                <div class="flex items-center justify-between border-t border-brand-border px-6 py-4">
                    <x-button type="button" id="wizard-prev" variant="secondary" icon="arrow-left" class="invisible">Précédent</x-button>
                    <x-button type="button" id="wizard-next">Suivant</x-button>
                </div>
            @endif
        </x-card>

        <div id="wizard-final-section" @if ($pageIndex > 0) style="display:none" @endif class="space-y-4">
            <x-card>
                <label for="priority" class="mb-1.5 block text-[13px] font-medium text-slate-700">Urgence de cette demande</label>
                <select id="priority" name="priority"
                        class="block w-full max-w-xs rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                    @foreach (\App\Enums\FormPriority::cases() as $priority)
                        <option value="{{ $priority->value }}" @selected(old('priority', $priority->value) === $priority->value)>{{ $priority->label() }}</option>
                    @endforeach
                </select>
                <p class="mt-1.5 text-xs text-slate-400">Visible par le validateur traitant cette demande - à toi de juger si celle-ci l'est, indépendamment du formulaire utilisé.</p>
            </x-card>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <x-button type="submit">Envoyer la demande</x-button>
                    <x-button href="{{ route('workflow.my-requests.select-form') }}" variant="secondary">Annuler</x-button>
                </div>
                <p id="autosave-status" class="text-xs text-slate-400"></p>
            </div>
        </div>
    </form>

    <div class="mt-3 max-w-2xl">
        <x-confirm-form
            :action="route('workflow.my-requests.destroy', $draft)"
            method="DELETE"
            confirm="Supprimer ce brouillon ? Tout ce qui a été saisi (et les pièces jointes déjà ajoutées) sera définitivement perdu."
            variant="ghost" icon="trash"
        >Supprimer le brouillon</x-confirm-form>
    </div>

    <script>
        (function () {
            var form = document.getElementById('request-form');
            var status = document.getElementById('autosave-status');
            if (!form || !status) return;

            // Champs "select" avec option "Autre" (voir
            // FieldOption::FREE_TEXT_VALUE) : bascule l'affichage vers un
            // texte libre dès que cette option est choisie. Les deux
            // partagent volontairement le même `name` - le texte libre,
            // placé après dans le DOM, l'emporte naturellement sur le
            // select à la soumission tant qu'il est actif (et
            // inversement, désactivé, il est tout simplement absent de
            // l'envoi).
            document.querySelectorAll('[data-other-select]').forEach(function (select) {
                var key = select.getAttribute('data-other-select');
                var input = form.querySelector('[data-other-input="' + key + '"]');
                if (!input) return;

                select.addEventListener('change', function () {
                    var isOther = select.value === '__AUTRE__';
                    input.classList.toggle('hidden', !isOther);
                    input.disabled = !isOther;
                    if (isOther) input.focus();
                });
            });

            // ==================================================
            // Champ "Montant" - un champ visible (affiché, formaté
            // "150 000,00") synchronisé vers un champ caché qui, lui,
            // porte le vrai nom du champ et une valeur numérique
            // propre ("150000.00") - c'est ce champ caché qui part
            // réellement à l'envoi et au brouillon automatique.
            // ==================================================
            document.querySelectorAll('[data-montant-display]').forEach(function (display) {
                var key = display.getAttribute('data-montant-display');
                var hidden = form.querySelector('[data-montant-value="' + key + '"]');
                if (!hidden) return;

                function syncRawValue() {
                    // Empêche aussi les lettres dans le champ visible
                    // lui-même (pas seulement dans la valeur cachée) -
                    // un montant ne doit jamais accepter de lettres,
                    // au même titre qu'un champ "code".
                    var cleanedDisplay = display.value.replace(/[^0-9,\s]/g, '');
                    if (cleanedDisplay !== display.value) {
                        display.value = cleanedDisplay;
                    }

                    var raw = display.value
                        .replace(/\s/g, '')
                        .replace(',', '.')
                        .replace(/[^0-9.]/g, '');
                    hidden.value = raw;
                }

                display.addEventListener('input', syncRawValue);

                display.addEventListener('blur', function () {
                    var num = parseFloat(hidden.value);
                    if (isNaN(num)) return;
                    display.value = num.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                });

                syncRawValue();
            });

            // ==================================================
            // Pagination par section - un formulaire long (35+
            // champs) est bien plus ergonomique découpé section par
            // section que déroulé d'un bloc. Purement visuel côté
            // navigateur : tous les champs restent dans le DOM et
            // partent ensemble à l'envoi - aucun changement côté
            // sauvegarde automatique, validation serveur ou soumission.
            // ==================================================
            var pages = Array.prototype.slice.call(document.querySelectorAll('.wizard-page'));
            var pageTitles = @json($pageTitles ?? []);
            if (pages.length > 1) {
                var current = 0;
                var progress = document.getElementById('wizard-progress');
                var progressLabel = document.getElementById('wizard-progress-label');
                var progressDots = document.getElementById('wizard-progress-dots');
                var prevBtn = document.getElementById('wizard-prev');
                var nextBtn = document.getElementById('wizard-next');
                var finalSection = document.getElementById('wizard-final-section');

                progress.classList.remove('hidden');
                progress.classList.add('flex');
                pages.forEach(function (_, i) {
                    var dot = document.createElement('span');
                    dot.className = 'h-1.5 w-1.5 rounded-full bg-brand-border';
                    dot.dataset.dot = i;
                    progressDots.appendChild(dot);
                });

                function render() {
                    pages.forEach(function (page, i) { page.style.display = i === current ? '' : 'none'; });
                    finalSection.style.display = current === pages.length - 1 ? '' : 'none';
                    prevBtn.classList.toggle('invisible', current === 0);
                    nextBtn.style.display = current === pages.length - 1 ? 'none' : '';
                    progressLabel.textContent = 'Section ' + (current + 1) + ' / ' + pages.length
                        + (pageTitles[current] ? ' — ' + pageTitles[current] : '');
                    progressDots.querySelectorAll('[data-dot]').forEach(function (dot) {
                        var isCurrent = Number(dot.dataset.dot) === current;
                        var isPast = Number(dot.dataset.dot) < current;
                        dot.className = 'h-1.5 w-1.5 rounded-full ' + (isCurrent ? 'bg-brand-blue' : isPast ? 'bg-brand-blue/40' : 'bg-brand-border');
                    });
                }

                function currentPageIsValid() {
                    var inputs = pages[current].querySelectorAll('input, select, textarea');
                    for (var i = 0; i < inputs.length; i++) {
                        if (!inputs[i].disabled && !inputs[i].checkValidity()) {
                            inputs[i].reportValidity();
                            return false;
                        }
                    }
                    return true;
                }

                // Après un envoi refusé par le serveur (ex. un champ
                // obligatoire resté vide), l'assistant rouvrait toujours
                // sur la première section - un champ en erreur situé
                // sur une AUTRE section devenait invisible, alors que le
                // message d'erreur, lui, s'affichait bien en haut de
                // l'écran. On saute donc directement à la première
                // section qui contient une erreur, s'il y en a une.
                var firstErroredPage = -1;
                pages.forEach(function (page, i) {
                    if (firstErroredPage === -1 && page.querySelector('.border-red-300')) {
                        firstErroredPage = i;
                    }
                });
                if (firstErroredPage !== -1) current = firstErroredPage;

                nextBtn.addEventListener('click', function () {
                    if (!currentPageIsValid()) return;
                    current = Math.min(current + 1, pages.length - 1);
                    render();
                    pages[current].scrollIntoView({ behavior: 'smooth', block: 'start' });
                });

                prevBtn.addEventListener('click', function () {
                    current = Math.max(current - 1, 0);
                    render();
                    pages[current].scrollIntoView({ behavior: 'smooth', block: 'start' });
                });

                // Filet de sécurité : empêche un Entrée dans un champ
                // texte de soumettre le formulaire avant la dernière
                // section (comportement natif du navigateur sinon).
                form.addEventListener('keydown', function (event) {
                    if (event.key === 'Enter' && event.target.tagName !== 'TEXTAREA' && current !== pages.length - 1) {
                        event.preventDefault();
                        nextBtn.click();
                    }
                });

                render();
            }

            var draftUrl = @json(route('workflow.my-requests.save-draft', $form));
            var saving = false;

            function saveDraft() {
                if (saving) return Promise.resolve();
                saving = true;
                status.textContent = 'Enregistrement du brouillon…';

                // Les champs de type "fichier" ne sont jamais inclus dans
                // le brouillon automatique - SaveRequestDraftRequest
                // n'accepte que du texte, et un fichier n'a de sens
                // qu'associé à la Request définitive (voir
                // SubmitRequestRequest::prepareForValidation()). On
                // reconstruit donc le FormData à la main plutôt que
                // d'utiliser `new FormData(form)` tel quel.
                var data = new FormData();
                Array.prototype.forEach.call(form.elements, function (el) {
                    if (!el.name || el.type === 'file' || el.disabled) return;
                    data.append(el.name, el.value);
                });

                return fetch(draftUrl, {
                    method: 'POST',
                    body: data,
                    headers: { 'Accept': 'application/json' },
                })
                    .then(function (response) { return response.ok ? response.json() : Promise.reject(); })
                    .then(function (data) {
                        status.textContent = 'Brouillon enregistré à ' + data.savedAt + '.';
                    })
                    .catch(function () {
                        status.textContent = "Échec de l'enregistrement automatique.";
                    })
                    .finally(function () { saving = false; });
            }

            // Sauvegarde automatique toutes les 20 secondes tant que
            // l'écran de saisie reste ouvert - une demande d'entreprise
            // peut prendre du temps à remplir (pièces jointes à
            // rassembler, chiffres à vérifier...), la perdre en fermant
            // l'onglet par erreur n'est pas acceptable.
            var interval = setInterval(saveDraft, 20000);

            // Arrêtée si l'utilisateur envoie réellement la demande ou
            // quitte la page, pour ne pas continuer à sauvegarder un
            // brouillon qui vient d'être supprimé côté serveur
            // (MyRequestController::store()).
            form.addEventListener('submit', function () { clearInterval(interval); });
            window.addEventListener('beforeunload', function () { clearInterval(interval); });
        })();
    </script>
@endsection