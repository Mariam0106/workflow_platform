{{--
    ==========================================================================
    <x-page-header>
    ==========================================================================
    AJOUT (round 3 - correction) : composant manquant dans le projet fourni.
    dashboard.blade.php (Étape 14, écrit à l'avance) appelait déjà
    <x-page-header> et <x-kpi-card>, mais aucun des deux fichiers de
    composant n'existait sous resources/views/components/ - d'où l'erreur
    "Unable to locate a class or view for component [page-header]" au
    premier accès au tableau de bord après connexion. Sans rapport avec
    la fonctionnalité de rôles multiples (N-N) des rounds précédents.

    Props : title (string), description (string|null)
    ==========================================================================
--}}
<div class="mb-6">
    <h1 class="text-[22px] font-semibold tracking-tight text-brand-navy">{{ $title }}</h1>
    @if ($description ?? null)
        <p class="mt-1 text-sm text-slate-500">{{ $description }}</p>
    @endif
</div>
