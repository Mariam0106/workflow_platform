@extends('layouts.admin', ['title' => 'Utilisateurs'])

@section('content')
    <x-page-header title="Utilisateurs" description="{{ $users->total() }} compte(s) au total">
        <x-slot:actions>
            <a href="{{ route('organisation.users.create') }}">
                <x-button>+ Nouvel utilisateur</x-button>
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="overflow-hidden rounded-xl border border-brand-border bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-brand-border bg-slate-50/70">
                <tr class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                    <th class="px-4 py-3">Nom</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Département</th>
                    <th class="px-4 py-3">Rôle</th>
                    <th class="px-4 py-3">Statut</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-border">
                @forelse ($users as $user)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-4 py-3 font-medium text-brand-navy">{{ $user->full_name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $user->email }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $user->department?->name }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $user->applicationRole?->name }}</td>
                        <td class="px-4 py-3">
                            <x-badge :color="$user->is_active ? 'success' : 'danger'">
                                {{ $user->is_active ? 'Actif' : 'Désactivé' }}
                            </x-badge>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('organisation.users.edit', $user) }}" class="text-sm font-medium text-brand-blue hover:underline">Modifier</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-sm text-slate-400">Aucun utilisateur pour le moment.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
@endsection
