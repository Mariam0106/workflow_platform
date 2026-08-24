@extends('layouts.admin', ['title' => 'Modifier ' . $field->label])

@section('content')
    <x-page-header :title="'Modifier « ' . $field->label . ' »'" :description="'Formulaire « ' . $form->name . ' »'">
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

    <div class="grid grid-cols-1 gap-4 {{ $field->isSelect() ? 'lg:grid-cols-2' : '' }}">
        <x-card>
            <form method="POST" action="{{ route('workflow.admin.forms.fields.update', [$form, $field]) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="section_title" class="mb-1.5 block text-[13px] font-medium text-slate-700">Titre de section</label>
                    <input id="section_title" name="section_title" type="text" list="existing-section-titles" value="{{ old('section_title', $field->section_title) }}"
                           class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                    <datalist id="existing-section-titles">
                        @foreach ($existingSectionTitles as $title)
                            <option value="{{ $title }}"></option>
                        @endforeach
                    </datalist>
                    @error('section_title') <p class="mt-1 text-xs text-brand-danger">{{ $message }}</p> @enderror
                    <p class="mt-1 text-xs text-slate-400">Optionnel. Affiche ce titre au-dessus de ce champ pour regrouper visuellement les champs suivants. Laisse vide pour rester dans la section précédente.</p>
                </div>

                <x-form-input name="label" label="Libellé" required :value="$field->label" />

                <div>
                    <label for="field_type" class="mb-1.5 block text-[13px] font-medium text-slate-700">Type de champ <span class="text-brand-danger">*</span></label>
                    <select id="field_type" name="field_type" required
                            class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                        @foreach ($fieldTypes as $type)
                            <option value="{{ $type }}" @selected(old('field_type', $field->field_type) === $type)>{{ $typeLabels[$type] ?? $type }}</option>
                        @endforeach
                    </select>
                    @error('field_type') <p class="mt-1 text-xs text-brand-danger">{{ $message }}</p> @enderror
                </div>

                <x-form-input name="placeholder" label="Texte indicatif" :value="$field->placeholder" hint="Optionnel." />
                <x-form-input name="default_value" label="Valeur par défaut" :value="$field->default_value" hint="Optionnel." />
                <x-form-checkbox name="is_required" label="Champ obligatoire" :checked="$field->is_required" />

                <div class="flex items-center gap-2.5">
                    <x-button type="submit" size="sm" icon="check">Enregistrer</x-button>
                </div>
            </form>
        </x-card>

        @if ($field->isSelect())
            <x-card :padded="false">
                <div class="border-b border-brand-border px-5 py-4">
                    <h2 class="text-[13px] font-semibold uppercase tracking-wide text-slate-500">
                        Options de la liste ({{ $field->fieldOptions->count() }})
                    </h2>
                </div>

                @if ($field->fieldOptions->isEmpty())
                    <x-empty-state icon="file" title="Aucune option" description="Ajoutez au moins une option pour que cette liste déroulante soit utilisable." />
                @else
                    <ul class="divide-y divide-brand-border">
                        @foreach ($field->fieldOptions as $option)
                            <li class="flex items-center gap-3 px-5 py-3">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-brand-navy">{{ $option->label }}</p>
                                    <p class="truncate text-xs text-slate-400">{{ $option->value }}</p>
                                </div>
                                @if ($option->is_default)
                                    <x-badge tone="blue">Par défaut</x-badge>
                                @else
                                    <form method="POST" action="{{ route('workflow.admin.forms.fields.options.default', [$form, $field, $option]) }}">
                                        @csrf
                                        <x-button type="submit" variant="ghost" size="sm">Définir par défaut</x-button>
                                    </form>
                                @endif
                                <x-confirm-form
                                    :action="route('workflow.admin.forms.fields.options.destroy', [$form, $field, $option])"
                                    method="DELETE"
                                    :confirm="'Supprimer l\'option « ' . ($option->label) . ' » ?'"
                                    variant="ghost" icon="trash"
                        title="Supprimer"><span class="sr-only">Supprimer</span></x-confirm-form>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <form method="POST" action="{{ route('workflow.admin.forms.fields.options.store', [$form, $field]) }}" class="border-t border-brand-border p-5">
                    @csrf
                    <p class="mb-3 text-[13px] font-medium text-slate-700">Ajouter une option</p>
                    <div class="grid grid-cols-2 gap-3">
                        <x-form-input name="value" label="Valeur" required hint="Ex. « MA »" />
                        <x-form-input name="label" label="Libellé affiché" required hint="Ex. « Maroc »" />
                    </div>
                    <div class="mt-3">
                        <x-form-checkbox name="is_default" label="Option par défaut" />
                    </div>
                    <x-button type="submit" size="sm" class="mt-3" icon="plus">Ajouter l'option</x-button>
                </form>
            </x-card>
        @endif
    </div>
@endsection
