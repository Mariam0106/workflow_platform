@extends('layouts.admin', ['title' => 'Départements'])

@section('content')
    <x-page-header title="Départements" description="{{ $departments->total() }} département(s) au total">
        <x-slot:actions>
            <a href="{{ route('organisation.departments.create') }}">
                <x-button>+ Nouveau département</x-button>
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="overflow-hidden rounded-xl border border-brand-border bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-brand-border bg-slate-50/70">
                <tr class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                    <th class="px-4 py-3">Nom</th>
                    <th class="px-4 py-3">Code</th>
                    <th class="px-4 py-3">Entité</th>
                    <th class="px-4 py-3">Statut</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-border">
                @forelse ($departments as $department)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-4 py-3 font-medium text-brand-navy">{{ $department->name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $department->code }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $department->entity?->name }}</td>
                        <td class="px-4 py-3">
                            <x-badge :color="$department->is_active ? 'success' : 'neutral'">
                                {{ $department->is_active ? 'Actif' : 'Archivé' }}
                            </x-badge>
                        </td>
                        <td class="space-x-3 px-4 py-3 text-right text-sm font-medium">
                            <a href="{{ route('organisation.departments.edit', $department) }}" class="text-brand-blue hover:underline">Modifier</a>
                            <form method="POST" action="{{ route($department->is_active ? 'organisation.departments.archive' : 'organisation.departments.restore', $department) }}" class="inline">
                                @csrf
                                <button class="{{ $department->is_active ? 'text-brand-danger' : 'text-brand-success' }} hover:underline">
                                    {{ $department->is_active ? 'Archiver' : 'Réactiver' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-400">Aucun département pour le moment.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $departments->links() }}</div>
@endsection
