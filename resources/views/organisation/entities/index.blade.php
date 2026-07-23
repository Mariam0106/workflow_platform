@extends('layouts.admin', ['title' => 'Entités'])

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-lg font-semibold">Entités</h1>
        <a href="{{ route('organisation.entities.create') }}" class="rounded-lg bg-brand-blue px-3.5 py-2 text-sm font-semibold text-white hover:bg-brand-blue-dark">
            + Nouvelle entité
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-brand-border bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-brand-border bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nom</th>
                    <th class="px-4 py-3">Code</th>
                    <th class="px-4 py-3">Statut</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-border">
                @foreach ($entities as $entity)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $entity->name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $entity->code }}</td>
                        <td class="px-4 py-3">
                            @if ($entity->is_active)
                                <span class="rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-brand-success">Actif</span>
                            @else
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-500">Archivé</span>
                            @endif
                        </td>
                        <td class="space-x-3 px-4 py-3 text-right">
                            <a href="{{ route('organisation.entities.edit', $entity) }}" class="text-brand-blue hover:underline">Modifier</a>
                            <form method="POST" action="{{ route($entity->is_active ? 'organisation.entities.archive' : 'organisation.entities.restore', $entity) }}" class="inline">
                                @csrf
                                <button class="{{ $entity->is_active ? 'text-brand-danger' : 'text-brand-success' }} hover:underline">
                                    {{ $entity->is_active ? 'Archiver' : 'Réactiver' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $entities->links() }}</div>
@endsection
