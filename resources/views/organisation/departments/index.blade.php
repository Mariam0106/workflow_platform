@extends('layouts.admin', ['title' => 'Départements'])

@section('content')
    <x-page-header title="Départements" description="{{ $departments->total() }} département(s)">
        <x-slot:actions>
            <x-button href="{{ route('organisation.departments.create') }}" icon="plus">Nouveau département</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4">
        <x-search-input :value="$search" placeholder="Rechercher un département…" />
    </div>

    <x-card :padded="false">
        @if ($departments->isEmpty())
            <x-empty-state icon="building" title="Aucun département" description="Créez le premier département de votre organisation.">
                <x-slot:actions>
                    <x-button href="{{ route('organisation.departments.create') }}" icon="plus">Nouveau département</x-button>
                </x-slot:actions>
            </x-empty-state>
        @else
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-brand-border text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                        <th class="px-5 py-3">Département</th>
                        <th class="px-5 py-3">Code</th>
                        <th class="px-5 py-3">Entité</th>
                        <th class="px-5 py-3">Statut</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border">
                    @foreach ($departments as $department)
                        <tr class="transition hover:bg-slate-50/60">
                            <td class="px-5 py-3.5 font-medium text-brand-navy">{{ $department->name }}</td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $department->code }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $department->entity?->name }}</td>
                            <td class="px-5 py-3.5"><x-status-badge :active="$department->is_active" activeLabel="Actif" inactiveLabel="Archivé" /></td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <x-button href="{{ route('organisation.departments.edit', $department) }}" variant="ghost" size="sm" icon="edit"
                        title="Modifier"><span class="sr-only">Modifier</span></x-button>
                                    @if ($department->is_active)
                                        <x-confirm-form
                                            :action="route('organisation.departments.archive', $department)"
                                            :confirm="'Archiver le département « ' . ($department->name) . ' » ? Il ne pourra plus recevoir de nouveaux utilisateurs.'"
                                            variant="ghost" icon="archive"
                        title="Archiver"><span class="sr-only">Archiver</span></x-confirm-form>
                                    @else
                                        <x-confirm-form
                                            :action="route('organisation.departments.restore', $department)"
                                            :confirm="'Réactiver le département « ' . ($department->name) . ' » ?'"
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
        <x-simple-paginator :paginator="$departments" />
    </x-card>
@endsection
