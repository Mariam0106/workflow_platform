@extends('layouts.admin', ['title' => 'Modifier ' . $user->full_name])

@section('content')
    <x-page-header :title="'Modifier ' . $user->full_name" :description="$user->email">
        <x-slot:actions>
            <x-button href="{{ route('organisation.users.show', $user) }}" variant="secondary" icon="arrow-left">Retour</x-button>
        </x-slot:actions>
    </x-page-header>

    <form method="POST" action="{{ route('organisation.users.update', $user) }}" class="max-w-2xl space-y-6">
        @csrf
        @method('PUT')

        <x-card>
            <h2 class="mb-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Identité</h2>
            <div class="grid grid-cols-2 gap-4">
                <x-form-input name="first_name" label="Prénom" required :value="$user->first_name" />
                <x-form-input name="last_name" label="Nom" required :value="$user->last_name" />
                <x-form-input name="phone" label="Téléphone" :value="$user->phone" hint="Optionnel" />
                <x-form-input name="employee_number" label="Matricule" :value="$user->employee_number" hint="Optionnel" />
                <div class="col-span-2">
                    <x-form-input name="job_title" label="Intitulé de poste" :value="$user->job_title" hint="Optionnel" />
                </div>
            </div>
            <p class="mt-3 text-xs text-slate-400">
                L'adresse e-mail ({{ $user->email }}) n'est pas modifiable depuis cet écran.
            </p>
        </x-card>

        <x-card>
            <h2 class="mb-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Rattachement organisationnel</h2>
            <div class="grid grid-cols-2 gap-4">
                <x-form-select name="entity_id" label="Entité" :options="$entities" required :value="$user->entity_id" />
                <x-form-select name="department_id" label="Département" :options="$departments" required :value="$user->department_id" />
                <x-form-select name="business_function_id" label="Fonction" :options="$businessFunctions" required :value="$user->business_function_id" />
                <x-form-select name="manager_id" label="Responsable hiérarchique (N+1)" :options="$managers->where('id', '!=', $user->id)" labelKey="full_name" placeholder="Aucun (sommet de la hiérarchie)" :value="$user->manager_id" />
            </div>
        </x-card>

        <x-card>
            <h2 class="mb-1 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Rôles applicatifs</h2>
            <p class="mb-4 text-xs text-slate-500">Un utilisateur peut détenir plusieurs rôles ; un seul est actif à la fois lors de sa connexion.</p>

            <div class="grid grid-cols-2 gap-2.5" role="group" aria-label="Rôles applicatifs">
                @php $currentRoleIds = old('application_role_ids', $user->applicationRoles->pluck('id')->all()); @endphp
                @foreach ($applicationRoles as $role)
                    <label class="flex cursor-pointer items-center gap-2.5 rounded-lg border border-brand-border px-3.5 py-2.5 text-sm text-brand-navy transition hover:border-brand-blue/40">
                        <input type="checkbox" name="application_role_ids[]" value="{{ $role->id }}"
                               @checked(collect($currentRoleIds)->contains($role->id))
                               class="h-4 w-4 rounded border-slate-300 text-brand-blue focus:ring-2 focus:ring-brand-blue/30">
                        {{ $role->name }}
                    </label>
                @endforeach
            </div>
            @error('application_role_ids') <p class="mt-2 text-xs text-brand-danger">{{ $message }}</p> @enderror

            <div class="mt-4">
                <x-form-select name="default_application_role_id" label="Rôle par défaut" :options="$applicationRoles" required
                                :value="$user->default_application_role_id"
                                hint="Doit faire partie des rôles cochés ci-dessus." />
            </div>
        </x-card>

        <x-card>
            <h2 class="mb-4 text-[13px] font-semibold uppercase tracking-wide text-slate-500">Statut</h2>
            <x-form-checkbox name="is_active" label="Compte actif" :checked="$user->is_active" hint="Un compte inactif ne peut pas se connecter." />
        </x-card>

        <div class="flex items-center gap-2.5">
            <x-button type="submit" icon="check">Enregistrer</x-button>
            <x-button href="{{ route('organisation.users.show', $user) }}" variant="secondary">Annuler</x-button>
        </div>
    </form>
@endsection
