<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Workflow Platform' }} — Saint-Gobain Maroc</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Sidebar rétractable (icônes seules <-> icônes + libellés) -
           en CSS pur plutôt qu'en variantes Tailwind arbitraires, pour
           rester prévisible sans avoir à recompiler pour vérifier.
           Appliqué sur <html> (pas <body>) pour pouvoir être posé par
           un script bloquant dans <head>, avant le premier rendu, et
           éviter tout effet de clignotement au chargement. */
        .sidebar { width: 16rem; transition: width 0.15s ease; }
        html.sidebar-collapsed .sidebar { width: 5rem; }
        html.sidebar-collapsed .sidebar-label { display: none; }
        html.sidebar-collapsed .sidebar-brand-text { display: none; }
        html.sidebar-collapsed .sidebar-section-title { display: none; }
        html.sidebar-collapsed .sidebar-toggle-icon { transform: rotate(180deg); }
        /* Sidebar réellement fixe à l'écran (indépendante du défilement
           de la page) - `position: sticky` ne suffisait pas ici car il
           dépend du conteneur défilant ; `fixed` détache complètement
           la sidebar du flux et garantit qu'elle reste immobile, quel
           que soit le contenu de la page. Le contenu principal reçoit
           en contrepartie une marge gauche égale à la largeur de la
           sidebar, ajustée elle aussi lors du repli/dépli. */
        .content-area { margin-left: 16rem; transition: margin-left 0.15s ease; }
        html.sidebar-collapsed .content-area { margin-left: 5rem; }
        /* Logo : jamais rétréci, toujours visible et central que la
           sidebar soit repliée ou non - c'est ce point précis qui posait
           problème avant (le logo devenait quasi invisible une fois la
           sidebar réduite, écrasé par le reste de la ligne). */
        html.sidebar-collapsed .sidebar-logo-row { flex-direction: column; gap: 0.5rem; padding-left: 0; padding-right: 0; align-items: center; }
    </style>
    <script>
        // Bloquant volontairement (avant le rendu du <body>) pour que
        // l'état replié/déplié soit déjà posé sur <html> avant que la
        // sidebar ne s'affiche - sans ça, on verrait la version dépliée
        // une fraction de seconde avant qu'elle ne se replie.
        if (localStorage.getItem('sidebarCollapsed') === '1') {
            document.documentElement.classList.add('sidebar-collapsed');
        }
    </script>
</head>
<body class="h-full bg-brand-bg text-brand-navy antialiased">
    <div class="flex min-h-screen">

        {{-- ==================================================
             SIDEBAR
        =================================================== --}}
        <aside class="sidebar fixed inset-y-0 left-0 z-30 flex h-screen shrink-0 flex-col border-r border-brand-border bg-white">
            <div class="sidebar-logo-row flex items-center justify-between gap-2 border-b border-brand-border px-4 py-4">
                <a href="{{ route('dashboard') }}" title="Retour au tableau de bord" class="flex min-w-0 items-center gap-2.5 rounded-lg transition hover:opacity-80">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-brand-border bg-white shadow-sm">
                        <img src="{{ asset('images/saint-gobain-logo.jpg') }}" alt="Saint-Gobain" class="h-full w-full object-contain">
                    </span>
                    <span class="sidebar-brand-text min-w-0">
                        <span class="block truncate text-[13px] font-semibold leading-tight text-brand-navy">Workflow Platform</span>
                        <span class="block truncate text-[11px] leading-tight text-slate-400">Saint-Gobain Maroc</span>
                    </span>
                </a>
                <button type="button" onclick="toggleSidebar()" title="Réduire / agrandir le menu"
                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-brand-navy">
                    <span class="sidebar-toggle-icon inline-flex transition-transform">
                        @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'h-4 w-4 -rotate-90'])
                    </span>
                </button>
            </div>

            <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
                @php
                    $activeRole = auth()->user()->activeApplicationRole();
                    $activeRoleCode = $activeRole?->code;
                @endphp

                {{-- Général (sans titre - l'icône Dashboard suffit à situer ces liens) --}}
                <div>
                    <ul class="space-y-0.5">
                        @include('layouts.partials.nav-item', ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'dashboard'])

                        {{-- Rôle actif = Utilisateur : soumet des demandes. --}}
                        @if ($activeRoleCode === \App\Enums\ApplicationRoleCode::User)
                            @include('layouts.partials.nav-item', ['label' => 'Nouvelle demande', 'route' => 'workflow.my-requests.select-form', 'icon' => 'plus'])
                            @include('layouts.partials.nav-item', ['label' => 'Mes demandes', 'route' => 'workflow.my-requests.index', 'routePattern' => 'workflow.my-requests.*', 'icon' => 'inbox'])
                        @endif

                        {{-- Rôle actif = Validateur : traite la file d'attente. --}}
                        @if ($activeRoleCode === \App\Enums\ApplicationRoleCode::Validator)
                            @include('layouts.partials.nav-item', ['label' => 'Mes validations', 'route' => 'workflow.my-validations.index', 'routePattern' => 'workflow.my-validations.*', 'icon' => 'check'])
                        @endif

                        @include('layouts.partials.nav-item', ['label' => 'Notifications', 'route' => 'workflow.notifications.index', 'routePattern' => 'workflow.notifications.*', 'icon' => 'bell'])
                    </ul>
                </div>

                {{-- Administration - visible uniquement si le Rôle ACTIF de
                     la session courante est Administrateur (BR-06) : un
                     Administrateur multi-rôle ayant switché sur "User" ne
                     voit plus cette section tant qu'il n'est pas revenu sur
                     "Administrateur". --}}
                @can('viewAny', \App\Models\User::class)
                    <div>
                        <p class="sidebar-section-title mb-1.5 px-2.5 text-[11px] font-semibold uppercase tracking-wide text-slate-400">Administration</p>
                        <ul class="space-y-0.5">
                            @include('layouts.partials.nav-item', ['label' => 'Utilisateurs', 'route' => 'organisation.users.index', 'routePattern' => 'organisation.users.*', 'icon' => 'users'])
                            @include('layouts.partials.nav-item', ['label' => 'Inscriptions en attente', 'route' => 'organisation.registrations.index', 'routePattern' => 'organisation.registrations.*', 'icon' => 'clock', 'badge' => \App\Models\User::query()->where('registration_status', \App\Enums\RegistrationStatus::Pending)->count()])
                            @include('layouts.partials.nav-item', ['label' => 'Départements', 'route' => 'organisation.departments.index', 'routePattern' => 'organisation.departments.*', 'icon' => 'building'])
                            @include('layouts.partials.nav-item', ['label' => 'Entités', 'route' => 'organisation.entities.index', 'routePattern' => 'organisation.entities.*', 'icon' => 'layers'])
                            @include('layouts.partials.nav-item', ['label' => 'Fonctions métier', 'route' => 'organisation.business-functions.index', 'routePattern' => 'organisation.business-functions.*', 'icon' => 'briefcase'])
                            @include('layouts.partials.nav-item', ['label' => 'Formulaires', 'route' => 'workflow.admin.forms.index', 'routePattern' => 'workflow.admin.forms.*', 'icon' => 'file'])
                            @include('layouts.partials.nav-item', ['label' => 'Workflow', 'route' => 'workflow.admin.workflows.index', 'routePattern' => 'workflow.admin.workflows.*', 'icon' => 'branch'])
                            @include('layouts.partials.nav-item', ['label' => 'Rapports', 'route' => 'workflow.admin.reports.index', 'icon' => 'chart'])
                            @include('layouts.partials.nav-item', ['label' => 'Historique', 'route' => 'workflow.admin.audit-logs.index', 'icon' => 'clock'])
                        </ul>
                    </div>
                @endcan
            </nav>

            {{-- Déconnexion --}}
            <div class="border-t border-brand-border p-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Se déconnecter"
                            class="flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-[13px] font-medium text-slate-500 transition hover:bg-red-50 hover:text-brand-danger">
                        @include('layouts.partials.icon', ['name' => 'logout', 'class' => 'h-[18px] w-[18px] shrink-0'])
                        <span class="sidebar-label">Se déconnecter</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- ==================================================
             CONTENU
        =================================================== --}}
        <div class="content-area flex-1">
            {{-- Bandeau supérieur : recherche, rôle actif, notifications, profil --}}
            <header class="flex h-14 items-center justify-between gap-4 border-b border-brand-border bg-white px-6">
                @include('layouts.partials.app-search')

                <div class="flex shrink-0 items-center gap-3">
                {{-- BR-06 : sélecteur de Rôle actif - affiché uniquement si
                     l'Utilisateur détient réellement plusieurs Rôles
                     autorisés. En <details>/<summary> (natif, accessible,
                     sans JS pour l'ouverture/fermeture) plutôt qu'un
                     <select> brut : le panneau ouvert est entièrement
                     stylisable, contrairement au rendu <select> natif du
                     navigateur qui ne l'est pas. --}}
                @if (auth()->user()->applicationRoles->count() > 1)
                    <details class="role-switch relative">
                        <summary class="flex h-9 cursor-pointer list-none items-center gap-2 rounded-full border border-brand-border bg-slate-50 px-3.5 text-[13px] font-medium text-brand-navy transition hover:border-brand-blue/40 hover:bg-white [&::-webkit-details-marker]:hidden">
                            @include('layouts.partials.icon', ['name' => 'switch', 'class' => 'h-3.5 w-3.5 text-brand-blue'])
                            {{ $activeRole?->name }}
                            @include('layouts.partials.icon', ['name' => 'chevron-down', 'class' => 'h-3.5 w-3.5 text-slate-400'])
                        </summary>
                        <div class="absolute right-0 z-20 mt-2 w-52 rounded-xl border border-brand-border bg-white p-1.5 shadow-lg">
                            <p class="px-2.5 pb-1.5 pt-1 text-[11px] font-semibold uppercase tracking-wide text-slate-400">Changer de rôle</p>
                            @foreach (auth()->user()->applicationRoles as $role)
                                @php $isCurrentRole = auth()->user()->activeApplicationRole()?->id === $role->id; @endphp
                                <form method="POST" action="{{ route('active-role.update') }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="application_role_id" value="{{ $role->id }}">
                                    <button type="submit"
                                            class="flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-left text-[13px] transition
                                                   {{ $isCurrentRole ? 'bg-brand-blue/[0.06] font-medium text-brand-blue' : 'text-brand-navy hover:bg-slate-50' }}">
                                        <span class="flex-1">{{ $role->name }}</span>
                                        @if ($isCurrentRole)
                                            @include('layouts.partials.icon', ['name' => 'check', 'class' => 'h-4 w-4 shrink-0'])
                                        @endif
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </details>
                @endif

                @php
                    $unreadCount = \App\Models\Notification::query()
                        ->where('recipient_id', auth()->id())
                        ->where('status', '!=', \App\Enums\NotificationStatus::Read)
                        ->count();
                @endphp
                <a href="{{ route('workflow.notifications.index') }}" title="Notifications"
                   class="relative flex h-9 w-9 items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100 hover:text-brand-navy">
                    @include('layouts.partials.icon', ['name' => 'bell', 'class' => 'h-[18px] w-[18px]'])
                    @if ($unreadCount > 0)
                        <span class="absolute -right-1 -top-1 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-brand-danger px-1 text-[10px] font-semibold text-white ring-2 ring-white">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('profile.show') }}" title="Mon profil"
                   class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-blue text-xs font-semibold text-white ring-2 ring-transparent transition hover:ring-brand-blue/25">
                    {{ mb_substr(auth()->user()->first_name ?? '', 0, 1) }}{{ mb_substr(auth()->user()->last_name ?? '', 0, 1) }}
                </a>
                </div>
            </header>

            <main class="mx-auto max-w-5xl px-8 py-8">
                @yield('content')
            </main>
        </div>
    </div>

    @if (session('status'))
        <div
            id="flash-toast"
            class="fixed right-6 top-6 z-50 flex max-w-sm items-start gap-3 rounded-xl border border-green-200 bg-white px-4 py-3.5 text-sm text-brand-navy shadow-lg [animation:flash-in_0.25s_ease-out]"
        >
            <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-green-50 text-brand-success">
                @include('layouts.partials.icon', ['name' => 'check', 'class' => 'h-4 w-4'])
            </span>
            <p class="flex-1 pt-0.5">{{ session('status') }}</p>
            <button type="button" onclick="document.getElementById('flash-toast').remove()"
                    class="mt-0.5 shrink-0 text-slate-400 transition hover:text-slate-600">
                @include('layouts.partials.icon', ['name' => 'close', 'class' => 'h-4 w-4'])
            </button>
        </div>
        <style>
            @keyframes flash-in { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }
            @keyframes flash-out { from { opacity: 1; } to { opacity: 0; transform: translateY(-6px); } }
        </style>
        <script>
            // Se ferme seul après quelques secondes - une notification qui
            // reste indéfiniment jusqu'au rechargement de la page n'est
            // pas une confirmation, c'est du bruit permanent.
            setTimeout(function () {
                var toast = document.getElementById('flash-toast');
                if (!toast) return;
                toast.style.animation = 'flash-out 0.2s ease-in forwards';
                setTimeout(function () { toast.remove(); }, 200);
            }, 4000);
        </script>
    @endif

    {{-- Affichage centralisé des erreurs métier (DomainException) : ces
         exceptions remontent via le sac $errors standard de Laravel
         (ex. "impossible de publier, aucune étape"), affichées ici une
         seule fois pour tout le Back Office plutôt que dupliquées sur
         chaque écran. --}}
    @if ($errors->any())
        <div
            id="flash-error-toast"
            class="fixed right-6 top-6 z-50 flex max-w-sm items-start gap-3 rounded-xl border border-red-200 bg-white px-4 py-3.5 text-sm text-brand-navy shadow-lg [animation:flash-in_0.25s_ease-out]"
        >
            <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-red-50 text-brand-danger">
                @include('layouts.partials.icon', ['name' => 'alert', 'class' => 'h-4 w-4'])
            </span>
            <p class="flex-1 pt-0.5">{{ $errors->first() }}</p>
            <button type="button" onclick="document.getElementById('flash-error-toast').remove()"
                    class="mt-0.5 shrink-0 text-slate-400 transition hover:text-slate-600">
                @include('layouts.partials.icon', ['name' => 'close', 'class' => 'h-4 w-4'])
            </button>
        </div>
        <style>
            @keyframes flash-in { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }
        </style>
        {{-- Volontairement pas d'auto-fermeture pour une erreur (contrairement
             au toast de succès ci-dessus) - manquer un message d'erreur
             qui explique pourquoi une action a été refusée est plus grave
             que de devoir cliquer une fois pour le fermer. --}}
    @endif

    <script>
        function toggleSidebar() {
            document.documentElement.classList.toggle('sidebar-collapsed');
            localStorage.setItem(
                'sidebarCollapsed',
                document.documentElement.classList.contains('sidebar-collapsed') ? '1' : '0'
            );
        }

        // Ferme le menu de changement de rôle (<details>) dès qu'on
        // clique ailleurs sur la page - un <details> natif reste sinon
        // ouvert indéfiniment tant qu'on ne reclique pas dessus.
        document.addEventListener('click', function (event) {
            document.querySelectorAll('details.role-switch[open]').forEach(function (el) {
                if (!el.contains(event.target)) {
                    el.removeAttribute('open');
                }
            });
        });
    </script>
</body>
</html>