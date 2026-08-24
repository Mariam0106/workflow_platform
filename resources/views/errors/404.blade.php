<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page introuvable — Workflow Platform</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-brand-bg px-6 text-brand-navy antialiased">
    <div class="w-full max-w-md text-center">
        <span class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
            <svg viewBox="0 0 18 18" fill="none" class="h-7 w-7"><circle cx="8" cy="8" r="5" stroke="currentColor" stroke-width="1.5"/><path d="M15 15l-3.4-3.4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </span>
        <p class="text-[13px] font-semibold uppercase tracking-wide text-slate-400">Erreur 404</p>
        <h1 class="mt-1 text-xl font-semibold text-brand-navy">Page introuvable</h1>
        <p class="mt-2 text-sm text-slate-500">
            Cette page n'existe pas ou plus - elle a peut-être été supprimée, ou le lien utilisé n'est plus valide.
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
