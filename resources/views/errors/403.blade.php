<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accès refusé — Workflow Platform</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-brand-bg px-6 text-brand-navy antialiased">
    <div class="w-full max-w-md text-center">
        <span class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50 text-brand-danger">
            <svg viewBox="0 0 18 18" fill="none" class="h-7 w-7"><path d="M9 2.3l5.5 2v3.5c0 4-2.4 6.7-5.5 7.9-3.1-1.2-5.5-3.9-5.5-7.9V4.3L9 2.3z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M9 6.5v3M9 12h.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </span>
        <p class="text-[13px] font-semibold uppercase tracking-wide text-slate-400">Erreur 403</p>
        <h1 class="mt-1 text-xl font-semibold text-brand-navy">Accès refusé</h1>
        <p class="mt-2 text-sm text-slate-500">
            Vous n'avez pas les droits nécessaires pour cette action - le rôle actif de votre session
            ne le permet pas, ou l'élément concerné n'est plus modifiable (par exemple : déjà publié).
        </p>
        <div class="mt-6 flex items-center justify-center gap-2.5">
            <a href="{{ url('/dashboard') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-blue px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-blue/30 transition hover:bg-brand-blue-dark">
                Retour au tableau de bord
            </a>
            <button type="button" onclick="history.back()"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-brand-border bg-white px-4 py-2.5 text-sm font-semibold text-brand-navy shadow-sm transition hover:bg-slate-50">
                Page précédente
            </button>
        </div>
    </div>
</body>
</html>
