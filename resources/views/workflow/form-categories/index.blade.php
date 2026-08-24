@extends('layouts.admin', ['title' => 'Catégories de formulaires'])

@section('content')
    <x-page-header title="Catégories de formulaires" description="{{ $formCategories->count() }} catégorie(s)">
        <x-slot:actions>
            <x-button href="{{ route('workflow.admin.form-categories.create') }}" icon="plus">Nouvelle catégorie</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4">
        <x-search-input :value="$search" placeholder="Rechercher une catégorie…" />
    </div>

    <x-card :padded="false">
        @if ($formCategories->isEmpty())
            <x-empty-state icon="file" title="Aucune catégorie" description="Ex. Gestion Client, Ressources Humaines, Finance, Achats.">
                <x-slot:actions>
                    <x-button href="{{ route('workflow.admin.form-categories.create') }}" icon="plus">Nouvelle catégorie</x-button>
                </x-slot:actions>
            </x-empty-state>
        @else
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-brand-border text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                        <th class="px-5 py-3">Catégorie</th>
                        <th class="px-5 py-3">Code</th>
                        <th class="px-5 py-3">Formulaires</th>
                        <th class="px-5 py-3">Statut</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border">
                    @foreach ($formCategories as $formCategory)
                        <tr class="transition hover:bg-slate-50/60">
                            <td class="px-5 py-3.5 font-medium text-brand-navy">{{ $formCategory->name }}</td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $formCategory->code }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $formCategory->forms_count }}</td>
                            <td class="px-5 py-3.5"><x-status-badge :active="$formCategory->is_active" activeLabel="Active" inactiveLabel="Archivée" /></td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <x-button href="{{ route('workflow.admin.form-categories.edit', $formCategory) }}" variant="ghost" size="sm" icon="edit"
                        title="Modifier"><span class="sr-only">Modifier</span></x-button>
                                    @if ($formCategory->is_active)
                                        <x-confirm-form
                                            :action="route('workflow.admin.form-categories.archive', $formCategory)"
                                            :confirm="'Archiver la catégorie « ' . ($formCategory->name) . ' » ?'"
                                            variant="ghost" icon="archive"
                        title="Archiver"><span class="sr-only">Archiver</span></x-confirm-form>
                                    @else
                                        <x-confirm-form
                                            :action="route('workflow.admin.form-categories.restore', $formCategory)"
                                            :confirm="'Réactiver la catégorie « ' . ($formCategory->name) . ' » ?'"
                                            variant="ghost" icon="restore"
                        title="Réactiver"><span class="sr-only">Réactiver</span></x-confirm-form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </x-card>
@endsection
