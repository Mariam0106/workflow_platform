@extends('layouts.admin', ['title' => 'Nouvel utilisateur'])

@section('content')
    <x-page-header title="Nouvel utilisateur" description="Créer un compte et lui attribuer un ou plusieurs rôles applicatifs.">
        <x-slot:actions>
            <x-button href="{{ route('organisation.users.index') }}" variant="secondary" icon="arrow-left">Retour</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('organisation.users.store') }}" class="max-w-2xl space-y-6">
        @csrf

        <x-card>
            <h2 class="mb-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Identité</h2>
            <div class="grid grid-cols-2 gap-4">
                <x-form-input name="first_name" label="Prénom" required />
                <x-form-input name="last_name" label="Nom" required />
                <div class="col-span-2">
                    <x-form-input name="email" type="email" label="Adresse e-mail professionnelle" placeholder="prenom.nom@saint-gobain.com" required />
                </div>
                <x-form-input name="phone" label="Téléphone" hint="Optionnel" />
                <x-form-input name="employee_number" label="Matricule" hint="Optionnel" />
                <div class="col-span-2">
                    <x-form-input name="job_title" label="Intitulé de poste" hint="Optionnel" />
                </div>
            </div>
        </x-card>

        <x-card>
            <h2 class="mb-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Rattachement organisationnel</h2>
            <div class="grid grid-cols-2 gap-4">
                <x-form-select name="entity_id" label="Entité" :options="$entities" required />
                <x-form-select name="department_id" label="Département" :options="$departments" required />
                <x-form-select name="business_function_id" label="Fonction" :options="$businessFunctions" required />
                <x-form-select name="manager_id" label="Responsable hiérarchique (N+1)" :options="$managers" labelKey="full_name" placeholder="Aucun (sommet de la hiérarchie)" />
            </div>
        </x-card>

        <x-card>
            <h2 class="mb-1 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Rôles applicatifs</h2>
            <p class="mb-4 text-xs text-slate-500">Un utilisateur peut détenir plusieurs rôles ; un seul est actif à la fois lors de sa connexion.</p>

            <div class="grid grid-cols-2 gap-2.5" role="group" aria-label="Rôles applicatifs">
                @foreach ($applicationRoles as $role)
                    <label class="flex cursor-pointer items-center gap-2.5 rounded-lg border border-brand-border px-3.5 py-2.5 text-sm text-brand-navy transition hover:border-brand-blue/40">
                        <input type="checkbox" name="application_role_ids[]" value="{{ $role->id }}"
                               @checked(collect(old('application_role_ids', []))->contains($role->id))
                               class="h-4 w-4 rounded border-slate-300 text-brand-blue focus:ring-2 focus:ring-brand-blue/30">
                        {{ $role->name }}
                    </label>
                @endforeach
            </div>
            @error('application_role_ids') <p class="mt-2 text-xs text-brand-danger">{{ $message }}</p> @enderror

            <div class="mt-4">
                <x-form-select name="default_application_role_id" label="Rôle par défaut" :options="$applicationRoles" required
                                hint="Doit faire partie des rôles cochés ci-dessus - c'est le rôle proposé à la première connexion." />
            </div>
        </x-card>

        <x-card>
            <h2 class="mb-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Sécurité</h2>
            <div class="grid grid-cols-2 gap-4">
                <x-form-input name="password" type="password" label="Mot de passe" required hint="8 caractères min., majuscule, minuscule et chiffre." />
                <x-form-input name="password_confirmation" type="password" label="Confirmation" required />
            </div>
            <div class="mt-4">
                <x-form-checkbox name="is_active" label="Compte actif" :checked="true" hint="Un compte inactif ne peut pas se connecter." />
            </div>
        </x-card>

        <div class="flex items-center gap-2.5">
            <x-button type="submit" icon="plus">Créer l'utilisateur</x-button>
            <x-button href="{{ route('organisation.users.index') }}" variant="secondary">Annuler</x-button>
        </div>
    </form>
@endsection
