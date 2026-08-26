@extends('layouts.admin', ['title' => 'Utilisateurs'])

@section('content')
    <x-page-header title="Utilisateurs" description="{{ $users->total() }} compte(s) au total">
        <x-slot:actions>
            <x-button href="{{ route('organisation.users.create') }}" icon="plus">Nouvel utilisateur</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-4">
        <x-search-input :value="$search" placeholder="Rechercher un nom, un e-mail…" />
    </div>

    <x-card :padded="false">
        @if ($users->isEmpty())
            <x-empty-state icon="users" title="Aucun utilisateur" description="Créez le premier compte de la plateforme.">
                <x-slot:actions>
                    <x-button href="{{ route('organisation.users.create') }}" icon="plus">Nouvel utilisateur</x-button>
                </x-slot:actions>
            </x-empty-state>
        @else
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-brand-border text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                        <th class="px-5 py-3">Utilisateur</th>
                        <th class="px-5 py-3">Rattachement</th>
                        <th class="px-5 py-3">Rôles</th>
                        <th class="px-5 py-3">Statut</th>
                        <th class="px-5 py-3">
                            <a href="{{ route('organisation.users.index', array_merge(request()->except(['sort', 'direction']), [
                                'sort' => 'last_login_at',
                                'direction' => $sort === 'last_login_at' && $direction === 'desc' ? 'asc' : 'desc',
                            ])) }}" class="inline-flex items-center gap-1 hover:text-brand-navy">
                                Dernière connexion
                                @if ($sort === 'last_login_at')
                                    @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'h-3 w-3 transition-transform ' . ($direction === 'asc' ? 'rotate-180' : '')])
                                @endif
                            </a>
                        </th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border">
                    @foreach ($users as $user)
                        <tr class="transition hover:bg-slate-50/60">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('organisation.users.show', $user) }}" class="group flex items-center gap-3">
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-blue/10 text-xs font-semibold text-brand-blue">
                                        {{ mb_substr($user->first_name, 0, 1) }}{{ mb_substr($user->last_name, 0, 1) }}
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block truncate font-medium text-brand-navy transition group-hover:text-brand-blue">{{ $user->full_name }}</span>
                                        <span class="block truncate text-xs text-slate-500">{{ $user->email }}</span>
                                    </span>
                                </a>
                            </td>
                            <td class="px-5 py-3.5 text-[13px] text-slate-600">
                                {{ $user->department?->name }}
                                <span class="text-slate-300">·</span>
                                {{ $user->entity?->name }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($user->applicationRoles as $role)
                                        <x-role-badge :role="$role" />
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <x-status-badge :active="$user->is_active" />
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">
                                @if ($user->last_login_at)
                                    {{ $user->last_login_at->diffForHumans() }}
                                @else
                                    <span class="text-slate-400">Jamais connecté</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <x-button href="{{ route('organisation.users.edit', $user) }}" variant="ghost" size="sm" icon="edit"
                        title="Modifier"><span class="sr-only">Modifier</span></x-button>
                                    @can('deactivate', $user)
                                        @if ($user->is_active)
                                            <x-confirm-form
                                                :action="route('organisation.users.deactivate', $user)"
                                                :confirm="'Désactiver ' . ($user->full_name) . ' ? Cette personne ne pourra plus se connecter.'"
                                                variant="ghost" icon="archive"
                        title="Désactiver"><span class="sr-only">Désactiver</span></x-confirm-form>
                                        @else
                                            <x-confirm-form
                                                :action="route('organisation.users.activate', $user)"
                                                :confirm="'Réactiver ' . ($user->full_name) . ' ?'"
                                                variant="ghost" icon="restore"
                        title="Réactiver"><span class="sr-only">Réactiver</span></x-confirm-form>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        <x-simple-paginator :paginator="$users" />
    </x-card>
@endsection
