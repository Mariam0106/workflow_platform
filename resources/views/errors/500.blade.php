<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Erreur inattendue — Workflow Platform</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-brand-bg px-6 text-brand-navy antialiased">
    <div class="w-full max-w-md text-center">
        <span class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-brand-warning">
            <svg viewBox="0 0 18 18" fill="none" class="h-7 w-7"><path d="M9 2.5l7.2 12.5a1 1 0 01-.87 1.5H2.67a1 1 0 01-.87-1.5L9 2.5z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M9 7.2v3.3M9 12.8h.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </span>
        <p class="text-[13px] font-semibold uppercase tracking-wide text-slate-400">Erreur 500</p>
        <h1 class="mt-1 text-xl font-semibold text-brand-navy">Une erreur inattendue est survenue</h1>
        <p class="mt-2 text-sm text-slate-500">
            Ce n'est pas de votre fait - réessayez dans un instant. Si le problème persiste, contactez votre administrateur.
        </p>
        <div class="mt-6 flex items-center justify-center gap-2.5">
            <a href="{{ url('/dashboard') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-blue px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-blue/30 transition hover:bg-brand-blue-dark">
                Retour au tableau de bord
            </a>
        </div>
    </div>
</body>
</html>
