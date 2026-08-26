@extends('layouts.auth', ['title' => 'Demande envoyée'])

@section('content')
    <div class="text-center">
        <span class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-blue/10 text-brand-blue">
            @include('layouts.partials.icon', ['name' => 'clock', 'class' => 'h-7 w-7'])
        </span>
        <h1 class="text-xl font-semibold text-brand-navy">Demande envoyée</h1>
        <p class="mt-2 text-sm text-slate-500">
            Ta demande d'inscription a bien été transmise à un Administrateur.
            Tu recevras un e-mail dès qu'elle aura été examinée - en attendant,
            ton compte n'est pas encore actif.
        </p>
        <a href="{{ route('login') }}"
           class="mt-6 inline-flex items-center justify-center gap-2 rounded-lg border border-brand-border bg-white px-4 py-2.5 text-sm font-semibold text-brand-navy shadow-sm transition hover:bg-slate-50">
            Retour à la connexion
        </a>
    </div>
@endsection
