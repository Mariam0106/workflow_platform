<?php
    // Liste des destinations accessibles à CET utilisateur pour SON rôle
    // actif (BR-06) - mêmes conditions que la sidebar, une seule source
    // de vérité en pratique (si un lien n'est pas dans la sidebar pour ce
    // rôle, il n'apparaît pas ici non plus).
    $searchItems = collect([
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'dashboard'],
    ]);

    if ($activeRoleCode === \App\Enums\ApplicationRoleCode::User) {
        $searchItems->push(['label' => 'Nouvelle demande', 'route' => 'workflow.my-requests.select-form', 'icon' => 'plus']);
        $searchItems->push(['label' => 'Mes demandes', 'route' => 'workflow.my-requests.index', 'icon' => 'inbox']);
    }

    if ($activeRoleCode === \App\Enums\ApplicationRoleCode::Validator) {
        $searchItems->push(['label' => 'Mes validations', 'route' => 'workflow.my-validations.index', 'icon' => 'check']);
        $searchItems->push(['label' => 'Mes décisions', 'route' => 'workflow.my-validations.history', 'icon' => 'clock']);
    }

    $searchItems->push(['label' => 'Notifications', 'route' => 'workflow.notifications.index', 'icon' => 'bell']);
    $searchItems->push(['label' => 'Mon profil', 'route' => 'profile.show', 'icon' => 'users']);

    if (auth()->user()->can('viewAny', \App\Models\User::class)) {
        $searchItems = $searchItems->concat([
            ['label' => 'Utilisateurs', 'route' => 'organisation.users.index', 'icon' => 'users'],
            ['label' => 'Nouvel utilisateur', 'route' => 'organisation.users.create', 'icon' => 'plus'],
            ['label' => 'Départements', 'route' => 'organisation.departments.index', 'icon' => 'building'],
            ['label' => 'Entités', 'route' => 'organisation.entities.index', 'icon' => 'layers'],
            ['label' => 'Fonctions métier', 'route' => 'organisation.business-functions.index', 'icon' => 'briefcase'],
            ['label' => 'Formulaires', 'route' => 'workflow.admin.forms.index', 'icon' => 'file'],
            ['label' => 'Catégories de formulaires', 'route' => 'workflow.admin.form-categories.index', 'icon' => 'file'],
            ['label' => 'Workflow', 'route' => 'workflow.admin.workflows.index', 'icon' => 'branch'],
            ['label' => 'Catégories de workflows', 'route' => 'workflow.admin.workflow-categories.index', 'icon' => 'branch'],
            ['label' => 'Rapports', 'route' => 'workflow.admin.reports.index', 'icon' => 'chart'],
            ['label' => 'Historique', 'route' => 'workflow.admin.audit-logs.index', 'icon' => 'clock'],
        ]);
    }

    $searchItemsJson = $searchItems->map(fn ($item) => [
        'label' => $item['label'],
        'href' => route($item['route']),
        'icon' => $item['icon'],
    ])->values()->toJson();
?>

<div id="app-search" class="relative w-full max-w-xs">
    <div class="relative">
        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
            <?php echo $__env->make('layouts.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </span>
        <input
            id="app-search-input"
            type="text"
            autocomplete="off"
            placeholder="Rechercher dans l'application…"
            class="h-9 w-full rounded-full border border-brand-border bg-slate-50 pl-9 pr-3 text-[13px] text-brand-navy transition placeholder:text-slate-400 hover:border-brand-blue/40 hover:bg-white focus:border-brand-blue focus:bg-white focus:outline-none focus:ring-4 focus:ring-brand-blue/10"
        >
    </div>

    <div id="app-search-results"
         class="absolute left-0 top-full z-30 mt-2 hidden max-h-80 w-full min-w-[16rem] overflow-y-auto rounded-xl border border-brand-border bg-white p-1.5 shadow-lg">
    </div>
</div>

<script>
    (function () {
        var allItems = <?php echo $searchItemsJson; ?>;
        var input = document.getElementById('app-search-input');
        var panel = document.getElementById('app-search-results');

        function iconSvg(name) {
            // Mini-jeu d'icônes en ligne pour le panneau de résultats -
            // évite un aller-retour serveur à chaque frappe. Sous-
            // ensemble volontairement réduit à ce qui est réellement
            // utilisé par $searchItems ci-dessus.
            var icons = {
                dashboard: '<path d="M3 10.5L9 4l6 6.5M4.5 9.5V15a1 1 0 001 1h3v-4h1v4h3a1 1 0 001-1V9.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>',
                plus: '<path d="M9 3.5v11M3.5 9h11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
                inbox: '<path d="M2.5 10h3.8l1 1.8h3.4l1-1.8h3.8" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M4 4.5h10l1.5 5.5v5a1 1 0 01-1 1h-11a1 1 0 01-1-1v-5L4 4.5z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>',
                check: '<path d="M4 9.5l3.2 3.2L14 5.8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>',
                clock: '<circle cx="9" cy="9" r="6.5" stroke="currentColor" stroke-width="1.5"/><path d="M9 5.5V9l2.5 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>',
                bell: '<path d="M5 8a4 4 0 118 0c0 3.2 1 4 1 4H4s1-.8 1-4z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M7.5 14a1.5 1.5 0 003 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
                users: '<circle cx="9" cy="6.5" r="2.5" stroke="currentColor" stroke-width="1.5"/><path d="M3.5 15c.6-2.6 2.7-4 5.5-4s4.9 1.4 5.5 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
                building: '<rect x="4" y="3" width="10" height="12.5" rx="1" stroke="currentColor" stroke-width="1.5"/><path d="M7 6.5h1M10 6.5h1M7 9.5h1M10 9.5h1M7 12.5h1M10 12.5h1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
                layers: '<path d="M9 3l6.5 3.5L9 10 2.5 6.5 9 3z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M2.5 10.5L9 14l6.5-3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>',
                briefcase: '<rect x="2.5" y="6" width="13" height="8.5" rx="1" stroke="currentColor" stroke-width="1.5"/><path d="M6.5 6V4.5a1 1 0 011-1h3a1 1 0 011 1V6" stroke="currentColor" stroke-width="1.5"/>',
                file: '<path d="M5.5 2.5h5l3 3v10a1 1 0 01-1 1h-7a1 1 0 01-1-1v-12a1 1 0 011-1z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M10 2.5V6h3.5" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>',
                branch: '<circle cx="5" cy="4.5" r="1.8" stroke="currentColor" stroke-width="1.5"/><circle cx="5" cy="13.5" r="1.8" stroke="currentColor" stroke-width="1.5"/><circle cx="13" cy="9" r="1.8" stroke="currentColor" stroke-width="1.5"/><path d="M5 6.3V11.7M6.5 8.2L11.3 9.8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
                chart: '<path d="M3 15.5V9M8 15.5V4.5M13 15.5v-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>'
            };
            return icons[name] || icons.file;
        }

        function render(items) {
            if (items.length === 0) {
                panel.innerHTML = '<p class="px-3 py-4 text-center text-[13px] text-slate-400">Aucun résultat.</p>';
                return;
            }
            panel.innerHTML = items.map(function (item) {
                return '<a href="' + item.href + '" class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-[13px] font-medium text-brand-navy transition hover:bg-brand-blue/[0.06] hover:text-brand-blue">' +
                    '<svg viewBox="0 0 18 18" fill="none" class="h-4 w-4 shrink-0 text-slate-400">' + iconSvg(item.icon) + '</svg>' +
                    '<span class="truncate">' + item.label + '</span>' +
                    '</a>';
            }).join('');
        }

        function openPanel() {
            var query = input.value.trim().toLowerCase();
            var filtered = query === ''
                ? allItems
                : allItems.filter(function (item) { return item.label.toLowerCase().indexOf(query) !== -1; });
            render(filtered);
            panel.classList.remove('hidden');
        }

        input.addEventListener('focus', openPanel);
        input.addEventListener('input', openPanel);

        document.addEventListener('click', function (event) {
            if (!document.getElementById('app-search').contains(event.target)) {
                panel.classList.add('hidden');
            }
        });

        input.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                panel.classList.add('hidden');
                input.blur();
            }
        });
    })();
</script>
<?php /**PATH C:\projects\to step 12 backup\resources\views/layouts/partials/app-search.blade.php ENDPATH**/ ?>