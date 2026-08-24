<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Session expirée — Workflow Platform</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-brand-bg px-6 text-brand-navy antialiased">
    <div class="w-full max-w-md text-center">
        <span class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-blue/10 text-brand-blue">
            <svg viewBox="0 0 18 18" fill="none" class="h-7 w-7"><circle cx="9" cy="9" r="6.5" stroke="currentColor" stroke-width="1.5"/><path d="M9 5.5V9l2.5 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        <p class="text-[13px] font-semibold uppercase tracking-wide text-slate-400">Session expirée</p>
        <h1 class="mt-1 text-xl font-semibold text-brand-navy">Votre session a expiré</h1>
        <p class="mt-2 text-sm text-slate-500">
            Vous êtes resté inactif trop longtemps, ou la page était ouverte depuis un moment.
            Reconnectez-vous pour continuer.
        </p>
        <div class="mt-6 flex items-center justify-center gap-2.5">
            <a href="{{ url('/login') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-blue px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-brand-blue/30 transition hover:bg-brand-blue-dark">
                Se reconnecter
            </a>
        </div>
    </div>
</body>
</html>
