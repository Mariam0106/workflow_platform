@extends('layouts.admin', ['title' => 'Utilisateurs'])

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-lg font-semibold">Utilisateurs</h1>
        <a href="{{ route('organisation.users.create') }}" class="rounded-lg bg-brand-blue px-3.5 py-2 text-sm font-semibold text-white hover:bg-brand-blue-dark">
            + Nouvel utilisateur
        </a>
    </div>

    <div class="overflow-hidden rounded-xl border border-brand-border bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-brand-border bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nom</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Département</th>
                    <th class="px-4 py-3">Rôle</th>
                    <th class="px-4 py-3">Statut</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-border">
                @foreach ($users as $user)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $user->full_name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $user->email }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $user->department?->name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $user->applicationRole?->name }}</td>
                        <td class="px-4 py-3">
                            @if ($user->is_active)
                                <span class="rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-brand-success">Actif</span>
                            @else
                                <span class="rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-brand-danger">Désactivé</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('organisation.users.edit', $user) }}" class="text-brand-blue hover:underline">Modifier</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
@endsection
