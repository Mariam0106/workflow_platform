@extends('layouts.admin', ['title' => 'Entités'])

@section('content')
    <x-page-header title="Entités" description="{{ $entities->total() }} entité(s)">
        <x-slot:actions>
            <x-button href="{{ route('organisation.entities.create') }}" icon="plus">Nouvelle entité</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4">
        <x-search-input :value="$search" placeholder="Rechercher une entité…" />
    </div>

    <x-card :padded="false">
        @if ($entities->isEmpty())
            <x-empty-state icon="layers" title="Aucune entité" description="Créez la première entité de votre organisation.">
                <x-slot:actions>
                    <x-button href="{{ route('organisation.entities.create') }}" icon="plus">Nouvelle entité</x-button>
                </x-slot:actions>
            </x-empty-state>
        @else
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-brand-border text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                        <th class="px-5 py-3">Entité</th>
                        <th class="px-5 py-3">Code</th>
                        <th class="px-5 py-3">Statut</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border">
                    @foreach ($entities as $entity)
                        <tr class="transition hover:bg-slate-50/60">
                            <td class="px-5 py-3.5 font-medium text-brand-navy">{{ $entity->name }}</td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $entity->code }}</td>
                            <td class="px-5 py-3.5"><x-status-badge :active="$entity->is_active" activeLabel="Actif" inactiveLabel="Archivé" /></td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <x-button href="{{ route('organisation.entities.edit', $entity) }}" variant="ghost" size="sm" icon="edit"
                        title="Modifier"><span class="sr-only">Modifier</span></x-button>
                                    @if ($entity->is_active)
                                        <x-confirm-form
                                            :action="route('organisation.entities.archive', $entity)"
                                            :confirm="'Archiver l\'entité « ' . ($entity->name) . ' » ? Elle ne pourra plus recevoir de nouveaux utilisateurs.'"
                                            variant="ghost" icon="archive"
                        title="Archiver"><span class="sr-only">Archiver</span></x-confirm-form>
                                    @else
                                        <x-confirm-form
                                            :action="route('organisation.entities.restore', $entity)"
                                            :confirm="'Réactiver l\'entité « ' . ($entity->name) . ' » ?'"
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
        <x-simple-paginator :paginator="$entities" />
    </x-card>
@endsection
