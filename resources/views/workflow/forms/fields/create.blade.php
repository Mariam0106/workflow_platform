@extends('layouts.admin', ['title' => 'Nouveau champ'])

@section('content')
    <x-page-header title="Nouveau champ" :description="'Formulaire « ' . $form->name . ' »'">
        <x-slot:actions>
            <x-button href="{{ route('workflow.admin.forms.edit', $form) }}" variant="secondary" icon="arrow-left">Retour</x-button>
        </x-slot:actions>
    </x-page-header>

    @php
        $typeLabels = [
            'text' => 'Texte court',
            'textarea' => 'Texte long',
            'email' => 'E-mail',
            'password' => 'Mot de passe',
            'number' => 'Nombre',
            'date' => 'Date',
            'select' => 'Liste déroulante',
            'file' => 'Pièce jointe',
        ];
    @endphp

    <form method="POST" action="{{ route('workflow.admin.forms.fields.store', $form) }}" class="max-w-xl space-y-4">
        @csrf
        <x-card>
            <div class="space-y-4">
                <div>
                    <label for="section_title" class="mb-1.5 block text-[13px] font-medium text-slate-700">Titre de section</label>
                    <input id="section_title" name="section_title" type="text" list="existing-section-titles" value="{{ old('section_title') }}"
                           class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                    <datalist id="existing-section-titles">
                        @foreach ($existingSectionTitles as $title)
                            <option value="{{ $title }}"></option>
                        @endforeach
                    </datalist>
                    @error('section_title') <p class="mt-1 text-xs text-brand-danger">{{ $message }}</p> @enderror
                    <p class="mt-1 text-xs text-slate-400">Optionnel. Affiche ce titre au-dessus de ce champ pour regrouper visuellement les champs suivants (ex. « Identité client »). Laisse vide pour rester dans la section précédente.</p>
                </div>

                <x-form-input name="label" label="Libellé" required hint="Affiché à l'utilisateur, ex. « Montant demandé »." />

                <div>
                    <label for="field_type" class="mb-1.5 block text-[13px] font-medium text-slate-700">Type de champ <span class="text-brand-danger">*</span></label>
                    <select id="field_type" name="field_type" required
                            class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                        @foreach ($fieldTypes as $type)
                            <option value="{{ $type }}" @selected(old('field_type') === $type)>{{ $typeLabels[$type] ?? $type }}</option>
                        @endforeach
                    </select>
                    @error('field_type') <p class="mt-1 text-xs text-brand-danger">{{ $message }}</p> @enderror
                    <p class="mt-1 text-xs text-slate-400">Le type « Liste déroulante » fait apparaître la configuration des options ci-dessous.</p>
                </div>

                <x-form-input name="placeholder" label="Texte indicatif" hint="Optionnel, ex. « Saisissez un montant en MAD ». " />
                <x-form-input name="default_value" label="Valeur par défaut" hint="Optionnel." />

                <x-form-checkbox name="is_required" label="Champ obligatoire" />

                {{-- ==================================================
                     OPTIONS DE LA LISTE — saisies directement ici,
                     inutile de créer le Champ d'abord puis revenir
                     l'éditer juste pour les ajouter. Masqué/affiché en
                     JS selon le Type de champ choisi ci-dessus.
                =================================================== --}}
                <div id="select-options-panel" class="hidden border-t border-brand-border pt-4">
                    <p class="mb-1.5 block text-[13px] font-medium text-slate-700">Options de la liste</p>
                    <p class="mb-3 text-xs text-slate-400">Un libellé par ligne - ex. « Weber-Isover », « LPM ».</p>

                    <div id="options-rows" class="space-y-2">
                        <input type="text" name="options[]" placeholder="Ex. Weber-Isover"
                               class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                        <input type="text" name="options[]" placeholder="Ex. LPM"
                               class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                    </div>

                    <button type="button" onclick="addOptionRow()"
                            class="mt-2 inline-flex items-center gap-1.5 text-[13px] font-medium text-brand-blue hover:text-brand-blue-dark">
                        @include('layouts.partials.icon', ['name' => 'plus', 'class' => 'h-3.5 w-3.5'])
                        Ajouter une option
                    </button>

                    <div class="mt-4 border-t border-brand-border pt-4">
                        <x-form-checkbox name="include_other_option" label="Ajouter une option « Autre » (texte libre)" :checked="true"
                                          hint="Si l'Utilisateur ne trouve pas son cas dans la liste, il peut taper ce qu'il veut. Sera l'option sélectionnée par défaut." />
                    </div>
                </div>
            </div>
        </x-card>

        <div class="flex items-center gap-2.5">
            <x-button type="submit">Ajouter le champ</x-button>
            <x-button href="{{ route('workflow.admin.forms.edit', $form) }}" variant="secondary">Annuler</x-button>
        </div>
    </form>

    <script>
        (function () {
            var typeSelect = document.getElementById('field_type');
            var panel = document.getElementById('select-options-panel');
            if (!typeSelect || !panel) return;

            function toggle() {
                panel.classList.toggle('hidden', typeSelect.value !== 'select');
            }

            typeSelect.addEventListener('change', toggle);
            toggle();
        })();

        function addOptionRow() {
            var rows = document.getElementById('options-rows');
            var input = document.createElement('input');
            input.type = 'text';
            input.name = 'options[]';
            input.placeholder = 'Autre option…';
            input.className = 'block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10';
            rows.appendChild(input);
            input.focus();
        }
    </script>
@endsection
