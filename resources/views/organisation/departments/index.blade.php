@extends('layouts.admin', ['title' => 'Départements'])

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-lg font-semibold">Départements</h1>
        <a href="{{ route('organisation.departments.create') }}" class="rounded-lg bg-brand-blue px-3.5 py-2 text-sm font-semibold text-white hover:bg-brand-blue-dark">
            + Nouveau département
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-brand-border bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-brand-border bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nom</th>
                    <th class="px-4 py-3">Code</th>
                    <th class="px-4 py-3">Entité</th>
                    <th class="px-4 py-3">Statut</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-border">
                @foreach ($departments as $department)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $department->name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $department->code }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $department->entity?->name }}</td>
                        <td class="px-4 py-3">
                            @if ($department->is_active)
                                <span class="rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-brand-success">Actif</span>
                            @else
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-500">Archivé</span>
                            @endif
                        </td>
                        <td class="space-x-3 px-4 py-3 text-right">
                            <a href="{{ route('organisation.departments.edit', $department) }}" class="text-brand-blue hover:underline">Modifier</a>
                            <form method="POST" action="{{ route($department->is_active ? 'organisation.departments.archive' : 'organisation.departments.restore', $department) }}" class="inline">
                                @csrf
                                <button class="{{ $department->is_active ? 'text-brand-danger' : 'text-brand-success' }} hover:underline">
                                    {{ $department->is_active ? 'Archiver' : 'Réactiver' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $departments->links() }}</div>
@endsection
