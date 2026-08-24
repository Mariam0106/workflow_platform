@extends('layouts.admin', ['title' => 'Mon profil'])

@section('content')
    <x-page-header title="Mon profil" :description="$user->email" />

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <x-card class="lg:col-span-2">
            <h2 class="mb-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Identité</h2>
            <dl class="grid grid-cols-2 gap-x-4 gap-y-3.5 text-sm">
                <div>
                    <dt class="text-xs text-slate-400">Nom complet</dt>
                    <dd class="mt-0.5 font-medium text-brand-navy">{{ $user->full_name }}</dd>
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
                    <dt class="text-xs text-slate-400">Intitulé de poste</dt>
                    <dd class="mt-0.5 text-brand-navy">{{ $user->job_title ?? '—' }}</dd>
                </div>
            </dl>
            <p class="mt-4 text-xs text-slate-400">
                Pour toute modification de ces informations, contactez votre administrateur.
            </p>
        </x-card>

        <x-card>
            <h2 class="mb-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Rôles applicatifs</h2>
            <div class="flex flex-wrap gap-1.5">
                @foreach ($user->applicationRoles as $role)
                    <x-role-badge :role="$role" />
                @endforeach
            </div>
        </x-card>

        <x-card class="lg:col-span-3">
            <h2 class="mb-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Rattachement organisationnel</h2>
            <dl class="grid grid-cols-2 gap-x-4 gap-y-3.5 text-sm sm:grid-cols-4">
                <div>
                    <dt class="text-xs text-slate-400">Entité</dt>
                    <dd class="mt-0.5 text-brand-navy">{{ $user->entity?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">Département</dt>
                    <dd class="mt-0.5 text-brand-navy">{{ $user->department?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">Fonction</dt>
                    <dd class="mt-0.5 text-brand-navy">{{ $user->businessFunction?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400">Responsable hiérarchique (N+1)</dt>
                    <dd class="mt-0.5 text-brand-navy">{{ $user->manager?->full_name ?? 'Aucun (sommet de la hiérarchie)' }}</dd>
                </div>
            </dl>
        </x-card>
    </div>
@endsection
