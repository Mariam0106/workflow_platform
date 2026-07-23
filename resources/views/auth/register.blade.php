@extends('layouts.auth', ['title' => 'Inscription'])

@section('width', 'max-w-[640px]')

@section('content')
    <div class="mb-8">
        <h1 class="text-[22px] font-semibold tracking-tight text-brand-navy">Créer un compte</h1>
        <p class="mt-1.5 text-sm text-slate-500">
            Réservé aux collaborateurs disposant d'une adresse professionnelle
            <span class="font-medium text-brand-navy">@saint-gobain.com</span>.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-6 flex items-start gap-2.5 rounded-lg border border-red-200 bg-red-50 p-3.5">
            <svg class="mt-0.5 h-4 w-4 shrink-0 text-red-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.63-1.516 2.63H3.72c-1.347 0-2.189-1.463-1.515-2.63L8.485 2.495ZM10 6a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 6Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
            </svg>
            <div class="text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-8" novalidate>
        @csrf

        {{-- ==================================================
             SECTION — Informations personnelles
        =================================================== --}}
        <section>
            <div class="mb-4 flex items-center gap-3">
                <h2 class="text-[13px] font-semibold uppercase tracking-wide text-brand-navy">Informations personnelles</h2>
                <div class="h-px flex-1 bg-brand-border"></div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="mb-1.5 block text-[13px] font-medium text-slate-700">Prénom</label>
                    <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required
                           class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                </div>
                <div>
                    <label for="last_name" class="mb-1.5 block text-[13px] font-medium text-slate-700">Nom</label>
                    <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required
                           class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                </div>

                <div class="col-span-2">
                    <label for="email" class="mb-1.5 block text-[13px] font-medium text-slate-700">Adresse e-mail professionnelle</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                           placeholder="prenom.nom@saint-gobain.com"
                           class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy placeholder:text-slate-400 shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                </div>

                <div class="col-span-2">
                    <label for="phone" class="mb-1.5 block text-[13px] font-medium text-slate-700">
                        Téléphone <span class="font-normal text-slate-400">(optionnel)</span>
                    </label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                           class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                </div>
            </div>
        </section>

        {{-- ==================================================
             SECTION — Rattachement organisationnel
        =================================================== --}}
        <section>
            <div class="mb-4 flex items-center gap-3">
                <h2 class="text-[13px] font-semibold uppercase tracking-wide text-brand-navy">Rattachement organisationnel</h2>
                <div class="h-px flex-1 bg-brand-border"></div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="entity_id" class="mb-1.5 block text-[13px] font-medium text-slate-700">Entité</label>
                    <select id="entity_id" name="entity_id" required
                            class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                        <option value="">Sélectionner —</option>
                        @foreach ($entities as $entity)
                            <option value="{{ $entity->id }}" @selected(old('entity_id') == $entity->id)>{{ $entity->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="department_id" class="mb-1.5 block text-[13px] font-medium text-slate-700">Département</label>
                    <select id="department_id" name="department_id" required
                            class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                        <option value="">Sélectionner —</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="business_function_id" class="mb-1.5 block text-[13px] font-medium text-slate-700">Fonction</label>
                    <select id="business_function_id" name="business_function_id" required
                            class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                        <option value="">Sélectionner —</option>
                        @foreach ($businessFunctions as $businessFunction)
                            <option value="{{ $businessFunction->id }}" @selected(old('business_function_id') == $businessFunction->id)>{{ $businessFunction->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="application_role_id" class="mb-1.5 block text-[13px] font-medium text-slate-700">Profil</label>
                    <select id="application_role_id" name="application_role_id" required
                            class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                        <option value="">Sélectionner —</option>
                        @foreach ($applicationRoles as $role)
                            <option value="{{ $role->id }}" @selected(old('application_role_id') == $role->id)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-2">
                    <label for="manager_id" class="mb-1.5 block text-[13px] font-medium text-slate-700">
                        Responsable hiérarchique (N+1) <span class="font-normal text-slate-400">(optionnel)</span>
                    </label>
                    <select id="manager_id" name="manager_id"
                            class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                        <option value="">Aucun (sommet de la hiérarchie)</option>
                        @foreach ($managers as $manager)
                            <option value="{{ $manager->id }}" @selected(old('manager_id') == $manager->id)>{{ $manager->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </section>

        {{-- ==================================================
             SECTION — Sécurité
        =================================================== --}}
        <section>
            <div class="mb-4 flex items-center gap-3">
                <h2 class="text-[13px] font-semibold uppercase tracking-wide text-brand-navy">Sécurité</h2>
                <div class="h-px flex-1 bg-brand-border"></div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="password" class="mb-1.5 block text-[13px] font-medium text-slate-700">Mot de passe</label>
                    <input id="password" type="password" name="password" required
                           class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                    <p class="mt-1.5 text-xs text-slate-400">8 caractères min., majuscule, minuscule et chiffre.</p>
                </div>
                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-[13px] font-medium text-slate-700">Confirmation</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
                </div>
            </div>
        </section>

        <button type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-lg bg-brand-blue px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-blue-dark focus:outline-none focus:ring-4 focus:ring-brand-blue/25">
            Créer mon compte
        </button>
    </form>

    <p class="mt-7 text-center text-sm text-slate-500">
        Déjà un compte ?
        <a href="{{ route('login') }}" class="font-medium text-brand-blue hover:text-brand-blue-dark hover:underline underline-offset-2">
            Se connecter
        </a>
    </p>
@endsection
