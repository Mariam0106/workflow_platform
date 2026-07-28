{{--
    AJOUT (post Étape 12, demande client) : écran de choix du rôle actif,
    affiché après l'authentification lorsque le User est autorisé pour
    plusieurs Application Roles (relation N-N - voir User::authorizedRoles()).
--}}
@extends('layouts.auth', ['title' => 'Choix du rôle'])

@section('content')
    <div class="mb-8">
        <h1 class="text-[22px] font-semibold tracking-tight text-brand-navy">Choisissez votre rôle</h1>
        <p class="mt-1.5 text-sm text-slate-500">
            Votre compte est autorisé pour plusieurs rôles. Sélectionnez celui avec lequel vous souhaitez continuer.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-5 flex items-start gap-2.5 rounded-lg border border-red-200 bg-red-50 p-3.5">
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

    <form method="POST" action="{{ route('role-selection.store') }}" class="space-y-3">
        @csrf

        @foreach ($roles as $role)
            <label class="flex cursor-pointer select-none items-center gap-3 rounded-lg border border-brand-border bg-white px-3.5 py-3 text-sm text-brand-navy shadow-sm transition hover:border-brand-blue/50 has-[:checked]:border-brand-blue has-[:checked]:bg-brand-blue/5">
                <input type="radio" name="application_role_id" value="{{ $role->id }}"
                       @checked(old('application_role_id', $currentRoleId) == $role->id)
                       class="h-4 w-4 border-slate-300 text-brand-blue focus:ring-2 focus:ring-brand-blue/30 focus:ring-offset-0">
                <span class="font-medium">{{ $role->name }}</span>
            </label>
        @endforeach

        <button type="submit"
                class="mt-4 flex w-full items-center justify-center gap-2 rounded-lg bg-brand-blue px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-blue-dark focus:outline-none focus:ring-4 focus:ring-brand-blue/25">
            Continuer
        </button>
    </form>
@endsection
