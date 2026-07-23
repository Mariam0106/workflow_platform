@extends('layouts.auth', ['title' => 'Connexion'])

@section('content')
    <div class="mb-8">
        <h1 class="text-[22px] font-semibold tracking-tight text-brand-navy">Bienvenue</h1>
        <p class="mt-1.5 text-sm text-slate-500">
            Authentifiez-vous pour accéder à vos demandes et validations.
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

    <form method="POST" action="{{ route('login') }}" class="space-y-5" novalidate>
        @csrf

        <div>
            <label for="email" class="mb-1.5 block text-[13px] font-medium text-slate-700">
                Adresse e-mail professionnelle
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   autocomplete="username" placeholder="prenom.nom@saint-gobain.com"
                   class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy placeholder:text-slate-400 shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
        </div>

        <div>
            <div class="mb-1.5 flex items-center justify-between">
                <label for="password" class="text-[13px] font-medium text-slate-700">Mot de passe</label>
            </div>
            <input id="password" type="password" name="password" required
                   autocomplete="current-password"
                   class="block w-full rounded-lg border border-brand-border bg-white px-3.5 py-2.5 text-sm text-brand-navy shadow-sm transition focus:border-brand-blue focus:outline-none focus:ring-4 focus:ring-brand-blue/10">
        </div>

        <label class="flex select-none items-center gap-2.5 text-sm text-slate-600">
            <input type="checkbox" name="remember"
                   class="h-4 w-4 rounded border-slate-300 text-brand-blue focus:ring-2 focus:ring-brand-blue/30 focus:ring-offset-0">
            Se souvenir de moi
        </label>

        <button type="submit"
                class="flex w-full items-center justify-center gap-2 rounded-lg bg-brand-blue px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-blue-dark focus:outline-none focus:ring-4 focus:ring-brand-blue/25">
            Connexion
        </button>
    </form>

    <p class="mt-7 text-center text-sm text-slate-500">
        Pas encore de compte ?
        <a href="{{ route('register') }}" class="font-medium text-brand-blue hover:text-brand-blue-dark hover:underline underline-offset-2">
            Créer un compte
        </a>
    </p>
@endsection
