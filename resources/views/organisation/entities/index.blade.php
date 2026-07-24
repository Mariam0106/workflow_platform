@extends('layouts.admin', ['title' => 'Entités'])

@section('content')
    <x-page-header title="Entités" description="{{ $entities->total() }} entité(s) au total">
        <x-slot:actions>
            <a href="{{ route('organisation.entities.create') }}">
                <x-button>+ Nouvelle entité</x-button>
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="overflow-hidden rounded-xl border border-brand-border bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-brand-border bg-slate-50/70">
                <tr class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                    <th class="px-4 py-3">Nom</th>
                    <th class="px-4 py-3">Code</th>
                    <th class="px-4 py-3">Statut</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-border">
                @forelse ($entities as $entity)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-4 py-3 font-medium text-brand-navy">{{ $entity->name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $entity->code }}</td>
                        <td class="px-4 py-3">
                            <x-badge :color="$entity->is_active ? 'success' : 'neutral'">
                                {{ $entity->is_active ? 'Actif' : 'Archivé' }}
                            </x-badge>
                        </td>
                        <td class="space-x-3 px-4 py-3 text-right text-sm font-medium">
                            <a href="{{ route('organisation.entities.edit', $entity) }}" class="text-brand-blue hover:underline">Modifier</a>
                            <form method="POST" action="{{ route($entity->is_active ? 'organisation.entities.archive' : 'organisation.entities.restore', $entity) }}" class="inline">
                                @csrf
                                <button class="{{ $entity->is_active ? 'text-brand-danger' : 'text-brand-success' }} hover:underline">
                                    {{ $entity->is_active ? 'Archiver' : 'Réactiver' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-400">Aucune entité pour le moment.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $entities->links() }}</div>
@endsection
