@extends('layouts.admin', ['title' => $user->full_name])

@section('content')
    <x-page-header :title="$user->full_name" :description="$user->email">
        <x-slot:actions>
            @can('update', $user)
                <x-button href="{{ route('organisation.users.edit', $user) }}" variant="secondary" icon="edit">Modifier</x-button>
            @endcan
            <x-button href="{{ route('organisation.users.index') }}" variant="secondary" icon="arrow-left">Retour</x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <x-card class="lg:col-span-2">
            <h2 class="mb-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Identité</h2>
            <dl class="grid grid-cols-2 gap-x-4 gap-y-3.5 text-sm">
                <div>
                    <dt class="text-xs text-slate-400">Nom complet</dt>
                    <dd class="mt-0.5 font-medium text-brand-navy">{{ $user->full_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">Statut</dt>
                    <dd class="mt-0.5"><x-status-badge :active="$user->is_active" /></dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">E-mail</dt>
                    <dd class="mt-0.5 text-brand-navy">{{ $user->email }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">Téléphone</dt>
                    <dd class="mt-0.5 text-brand-navy">{{ $user->phone ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">Matricule</dt>
                    <dd class="mt-0.5 text-brand-navy">{{ $user->employee_number ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">Intitulé de poste</dt>
                    <dd class="mt-0.5 text-brand-navy">{{ $user->job_title ?? '—' }}</dd>
                </div>
            </dl>
        </x-card>

        <x-card>
            <h2 class="mb-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Rôles applicatifs</h2>
            <div class="flex flex-wrap gap-1.5">
                @foreach ($user->applicationRoles as $role)
                    <x-role-badge :role="$role" />
                @endforeach
            </div>
            <p class="mt-3 text-xs text-slate-400">
                Rôle par défaut : <span class="font-medium text-slate-600">{{ $user->applicationRole?->name }}</span>
            </p>
        </x-card>

        <x-card class="lg:col-span-2">
            <h2 class="mb-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Rattachement organisationnel</h2>
            <dl class="grid grid-cols-2 gap-x-4 gap-y-3.5 text-sm">
                <div>
                    <dt class="text-xs text-slate-400">Entité</dt>
                    <dd class="mt-0.5 text-brand-navy">{{ $user->entity?->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">Département</dt>
                    <dd class="mt-0.5 text-brand-navy">{{ $user->department?->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">Fonction</dt>
                    <dd class="mt-0.5 text-brand-navy">{{ $user->businessFunction?->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">Responsable (N+1)</dt>
                    <dd class="mt-0.5 text-brand-navy">{{ $user->manager?->full_name ?? '—' }}</dd>
                </div>
            </dl>
        </x-card>

        <x-card>
            <h2 class="mb-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Actions</h2>
            <div class="space-y-2">
                @can('deactivate', $user)
                    @if ($user->is_active)
                        <x-confirm-form
                            :action="route('organisation.users.deactivate', $user)"
                            :confirm="'Désactiver ' . ($user->full_name) . ' ? Cette personne ne pourra plus se connecter.'"
                            variant="danger" icon="archive"
                        >Désactiver le compte</x-confirm-form>
                    @else
                        <x-confirm-form
                            :action="route('organisation.users.activate', $user)"
                            :confirm="'Réactiver ' . ($user->full_name) . ' ?'"
                            variant="secondary" icon="restore"
                        >Réactiver le compte</x-confirm-form>
                    @endif
                @else
                    <p class="text-xs text-slate-400">Aucune action disponible sur ce compte.</p>
                @endcan
            </div>
        </x-card>
    </div>
@endsection
