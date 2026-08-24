@extends('layouts.admin', ['title' => 'Fonctions métier'])

@section('content')
    <x-page-header title="Fonctions métier" description="{{ $businessFunctions->total() }} fonction(s)">
        <x-slot:actions>
            <x-button href="{{ route('organisation.business-functions.create') }}" icon="plus">Nouvelle fonction</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4">
        <x-search-input :value="$search" placeholder="Rechercher une fonction…" />
    </div>

    <x-card :padded="false">
        @if ($businessFunctions->isEmpty())
            <x-empty-state icon="briefcase" title="Aucune fonction métier" description="Ex. Commercial, Crédit Client, DAF, DG.">
                <x-slot:actions>
                    <x-button href="{{ route('organisation.business-functions.create') }}" icon="plus">Nouvelle fonction</x-button>
                </x-slot:actions>
            </x-empty-state>
        @else
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-brand-border text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                        <th class="px-5 py-3">Fonction</th>
                        <th class="px-5 py-3">Code</th>
                        <th class="px-5 py-3">Statut</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border">
                    @foreach ($businessFunctions as $businessFunction)
                        <tr class="transition hover:bg-slate-50/60">
                            <td class="px-5 py-3.5 font-medium text-brand-navy">{{ $businessFunction->name }}</td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $businessFunction->code }}</td>
                            <td class="px-5 py-3.5"><x-status-badge :active="$businessFunction->is_active" activeLabel="Active" inactiveLabel="Archivée" /></td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <x-button href="{{ route('organisation.business-functions.edit', $businessFunction) }}" variant="ghost" size="sm" icon="edit"
                        title="Modifier"><span class="sr-only">Modifier</span></x-button>
                                    @if ($businessFunction->is_active)
                                        <x-confirm-form
                                            :action="route('organisation.business-functions.archive', $businessFunction)"
                                            :confirm="'Archiver la fonction « ' . ($businessFunction->name) . ' » ? Elle ne pourra plus être attribuée à de nouveaux utilisateurs.'"
                                            variant="ghost" icon="archive"
                        title="Archiver"><span class="sr-only">Archiver</span></x-confirm-form>
                                    @else
                                        <x-confirm-form
                                            :action="route('organisation.business-functions.restore', $businessFunction)"
                                            :confirm="'Réactiver la fonction « ' . ($businessFunction->name) . ' » ?'"
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
        <x-simple-paginator :paginator="$businessFunctions" />
    </x-card>
@endsection
